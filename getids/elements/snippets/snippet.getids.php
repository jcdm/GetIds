<?php
/**
 * getIds
 *
 * A general purpose snippet to get a list of resource ids for MODx 2.0.
 *
 * @author Coroicor
 * @copyright Copyright 2010, Coroico
 * @version 1.0.0-pl - November 27, 2010
 *
 * [[!GetIds? &depth=`5` &ids=`8,-c10,12,3,c3` &sampleSize=`10` &invert=`1`]]
 *
 * depth - (Opt) Integer value indicating depth to search for resources from each parent
 *
 * ids - Comma-delimited list of resource ids serving as parents, child or resource
 *
 * ids as [ [+| |-] [c| |p|s]integer ] where:
 *
 *  - : exclude ids             + or '' : include ids (default)
 *  p : parents resources                           c : children resources
 *  s : provide a subsample of children             '': current resource
 *
 * sampleSize - number of children to provide in a subsample of children
 *
 * invert - inverts the output so that instead of short listing the IDs it lists IDs to remove in long form
 *
 * e.g:
 *
 * &ids=`18, c18, -c21, 34` : include #18 and children of #18, exclude chidren of #21 but keep #34
 *
 * &ids=`p12, -p3, -1, 2`   : include all parents of #12, exclude parents of #3 but keep #2
 *
 * &ids=`18, c18, p3, -p2`  : include #8 and children of #8, include parents of #3, exclude parent of #2
 *
 *
 * IMPORTANT: take care of the order of arguments. To be excluded the id should be already in tihe list
 * &ids=`18, 19, -19, 20` => '18,20'          but &ids=`18, -19, 19, 20` => '18,19,20'
 *
 */

/* set default properties */
$ids = (!empty($ids) || $ids === '0') ? explode(',', $ids) : array($modx->resource->get('id'));
$depth = isset($depth) ? (integer) $depth : $scriptProperties['depth'];
$sampleSize = (isset($sampleSize) && $sampleSize !== "") ? intval($sampleSize) : 10;

$ids = array_map('trim',$ids);
$resIds = array();

// Added by JCDM
// Loop through looking for 's' (samples) and if they exist, insert a '-c' before it to ensure on the samples make it in
$rules = array();
foreach($ids as $id) {
    if(strpos($id,"s") !== false) {
        // There is an 's'
        $rules[] = str_replace("s","-c",$id);
        $rules[] = $id;
    } else {
        // There is no 's'
        $rules[] = $id;
    }
}
$ids = $rules;
// /Added by JCDM

foreach ($ids as $id) {
    if (intval($id)) {  // specified without any prefix
        $id = ($id > 0) ? "+n".abs($id) : "-n".abs($id);
    }
    $len = strlen($id);
    $digit1 = substr( $id, 0, 1); // p,n, c or s
    $str = substr($id,1,strlen($id)-1);

    if ($len >= 3){
        //echo "3>";
        if (intval($str)) $id = '+' . $digit1 . abs($str);
        else if ($digit1 != '+' && $digit1 != '-') $id = substr($id,1,1) . $digit1 . substr($id,2,strlen($id)-2);
    }
    else if ($len == 2) {
        //echo "2>";
        if (intval($str) || $str==="0") $id = '+' . $digit1 . $str;     // JCDM added 'or' to allow for c0 case
        else $id = '';
    }
    else if ($len == 1){
        //echo "1>";
        if (intval($str)) $id = '+' . 'n' . $id;
        else $id = '';
    }
    //echo “$id - ";

    $digit1 = strtolower(substr( $id, 0, 1));
    $digit2 = strtolower(substr( $id, 1, 1));
    $rid = substr($id, 2, strlen($id)-2);

    switch($digit2){
        case "n":  // simple node
            $tmp = array($rid);
            break;
        case "c":  // children
            $tmp = $modx->getChildIds($rid, $depth);
            break;
        case "p":   // parents
            $tmp = $modx->getParentIds($rid, $depth);
            break;

// Added by JCDM
        case "s": // sampling of children
            $tmp = $modx->getChildIds($rid, $depth);
            sort($tmp);
//            echo "<pre>";
//            print_r($tmp);
//            echo "</pre>";

            if(count($tmp) > $sampleSize) {
//                echo count($tmp);
                sort($tmp); // Sort to make sure we get the same order every time
                $subsample = array();
                $subSample[] = $tmp[0];   // Put the very first value into the temp array
                $subSample[] = $tmp[0];
                for($i=1;$i<=$sampleSize-1;$i++) { // Minus 1 because we want sampleSize-1 pieces
                    //echo round(($i/$sampleSize) * count($tmp))-1 . " | ";
                    $subSample[] = $tmp[round(($i/$sampleSize) * count($tmp))-1];
                }
//                echo “ - ";
                $tmp = $subSample;
            }
//            echo count($tmp) . “ - ";
            break;
// /Added by JCDM
    }

    if ($digit1 == '+') $resIds = array_merge($resIds,$tmp);  // add ids
    else if ($digit1 == '-') $resIds = array_values(array_diff($resIds,$tmp));  // remove excluded ids
}

$resIds = array_values(array_unique($resIds));  // remove duplicated ids
//echo "<p>Count resIds: ". count($resIds) . "</p>";

// Added by JCDM
// Check for inversion - if we have one, figure out the diff with all IDs and return them as negatives
if ($invert == 1) {
    // Run GetIds to get the list of all ids
    $allIds = explode(",",$modx->runSnippet('GetIds',array(
        'ids' => 'c0',
        'invert' => 0
    )));

    // Run a diff on all IDs and the selected IDs to retrieve the 'neg' list
    $diffIds = array_diff($allIds,$resIds);

    // Loop through the diff and prepare an array with '-' prefixed for output
    $negList = array();
    foreach($diffIds as $id) {
        $negList[] = "-".$id;
    }

    // Check if the neg list is different in size - if it is, return the neg list, otherwise return the original list
    if(count($negList) > 1) {
        $lstIds = implode(",",$negList);
    } else {
        $lstIds = implode(',',$resIds);
    }

} else {
    // If we're not inverted, return the 'regular' ids list
    $lstIds = implode(',',$resIds);
};

return $lstIds;
//return ",".$lstIds.",";
