Nether Object
=====================================
[![Code Climate](https://codeclimate.com/github/netherphp/object/badges/gpa.svg)](https://codeclimate.com/github/netherphp/object)

This package provides a self-constructing object translation matrix capacitor.

It's kind of the cornerstone of all the things in Nether. It lets you do things like
translate database schemes that look like this:

	obj_id int
	obj_name string
	obj_date string
	
Into stuff you actually want to type in your code, such as...

	Object->ID
	Object->Name
	Object->Date

It also does a few other things, like it can be used to be sure beyond a doubt that the
random StdClass you are passing around in your app have the bare minimum properties
required, and other various intregrity reasons.

Obviously, not fully documented yet.

Install
-------------------------------------
To use it stand alone, Composer yourself a netherphp/object with a version of 1.*

If you are using any other Nether components you'll most likely already have this.

