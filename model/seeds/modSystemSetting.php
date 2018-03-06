<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(

  array(
    'key'  		=>     'getids.depth',
  	'value'		=>     '10',
    'xtype'		=>     'textfield',
    'namespace' => 'getids',
    'area' 		=> 'getids:default'
  ),
  array(
    'key'  		=>     'getids.subsample_size',
    'value'		=>     '10',
    'xtype'		=>     'textfield',
    'namespace' => 'getids',
    'area' 		=> 'getids:default'
  ),
);
/*EOF*/
