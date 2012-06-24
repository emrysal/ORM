<?php
/**
**  @file index.php
**  @author Dregian
**  @date 2012-06-24
**/
require_once('lib/SQLBuilder.php');
require_once('lib/Table.php');
require_once('lib/Model.php');

require_once('models/Example.php');

use ORM\Example;

// inserting
# $example = new Example();
# $example->name = 'Demo 1';
# $example->date = date('Y-m-d H:i:s');
# $example->save();

# $example->name = 'Demo 2';
# $example->date = date('Y-m-d H:i:s');
# $example->save();

// the create shortcut
# $example = Example::create(array(
# 	'name' => 'Ali',
# 	'date' => date('Y-m-d H:i:s')
# ));

// selecting
# var_dump(Example::all());
# var_dump(Example::first());
# var_dump(Example::last());
# var_dump(Example::find(array(
# 	'where' => array('id' => 109)
# )));

// updating
# $example = Example::find(144);
# $example->name = 'My new name is tom';
# $example->save();

// deleting
# $example = Example::find(112);
# $example->delete();
