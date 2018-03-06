# GetIds

Version: 1.2.1-rc1

Date: 2018.02.22

Authors: Coroicor, jcdm, jonathanhaslett

A general purpose snippet to get a list of resource ids for MODX Revolution.

Released under GNU Public License 2.0 https://opensource.org/licenses/GPL-2.0

## Usage

	[[!GetIds? &depth=`5` &ids=`8,-c10,12,3,c3` &sampleSize=`10` &invert=`1`]]

**depth** - (Opt) Integer value indicating depth to search for resources from each parent

**ids** - Comma-delimited list of resource ids serving as parents, child or resource

ids as `[ [+| |-] [c| |p|s]integer ]` where:

`-` : exclude ids

`+` or `''` : include ids (default)

`p` : parents resources

`c` : children resources

`s` : provide a subsample of children

`''` : current resource

**sampleSize** - number of children to provide in a subsample of children (defaults to 10 if not provided)

**invert** - inverts the output so that instead of short listing the IDs it lists IDs to remove in long form

## Examples

	&ids=`18, c18, -c21, 34`

This will include #18 and children of #18, exclude chidren of #21 but keep #34

	&ids=`p12, -p3, -1, 2`

This include all parents of #12, exclude parents of #3 but keep #2

	&ids=`c0, s18` &sampleSize=`5`

This will include all resources in the site, then remove all children of 18 and add
back 5 children of 18


## Note on subsampling


The way subsampling works is that it selects all child IDs and sorts them in numerical order. It always includes both the first and last options and then an even spacing in between. This makes
the results reliable in terms of always choosing the same resources when the same range is available.


**For example:** If there are 10 IDs numbered 1 to 10 and we ask for a subsample of 3, the results will reliably be
IDs 1,5,10.


**Important:** take care of the order of arguments. To be excluded the id should be already in tihe list
``` &ids=`18, 19, -19, 20` ``` => ``` '18,20' ``` but ``` &ids=`18, -19, 19, 20` ``` => ``` '18,19,20' ```.
