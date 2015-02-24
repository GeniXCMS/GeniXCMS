# Categories Class

```
@filename: Categories.class.php
@location: inc/lib/
@package: GeniXCMS
@since: 0.0.1
@author: Puguh Wijayanto (www.metalgenix.com)
@copyright: 2014-2015 Puguh Wijayanto
@license: MIT License
```

> This Documentation is still need improvement.

This class is to manage the Categories. This category structure is a very simple. 

An improvement probably added in the future. Below are the explanation each function at the Categories Class.

## Dropdown Function

Usage : `echo Categories::dropdown(array '$vars');`

This function was intended to create an automatic dropdown options from available categories at the database. This means that the developer can create dropdown easily just fill in the parameters and a **select input** created automatically.

Example:

```
$vars = array(
             'name'      =>  'catname',
             'parent'    =>  'parent',
             'order_by'  =>  '',
             'sort'      =>  'ASC',
           )
echo Categories::dropdown($vars);
```

That functions calls will create output like this.

```
<select name="catname" class="form-control">
	<option></option>
	<option value="1">Category Name</option>
	...
</select>

```

Explanation:

- **name**, this is the name  of the select input.
- **parent**, this is the parent of the category you want to show. No parent or *empty* means all categories will shows up. 
- **order_by**, this is how you want the category ordered by. Available columns are : `id`, `name`, `slug`, `parent`, `desc` default is `id`
- **sort**, this is the options how your categories sorted, Ascending `ASC`, or Descending `DESC`. Default is `ASC`


See also:

- [Db::result()](db.class.md)

  
## Name Function

Usage: `echo Categories::name(int '$id');`

This function will get the name of Category for the specific ID.

See also:

- [Db::result()](db.class.md)
  

## getParent Function

Usage: `echo Categories::getParent(int '$id');`

This is to get the parent of a speicific ID.

See also:

- [Db::result()](db.class.md)

  

## Delete Function

Usage: `Categories::delete(int '$id');`

This function will delete Category with a specific ID.

See also:

- [Db::result()](db.class.md)
.