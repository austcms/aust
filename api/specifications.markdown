API SPECIFICATIONS
====

Version: 0.0.1


Content Queries
====


1) Basic Content Queries
====

#1.1: order, limit and all fields

	Structure: News
	Module: content
	What we want: The last 10 records, all fields, ordered by name and id

	/api.json?query=News&order=name;id&limit=10&fields=*

#1.2: Unordered, specific fields

	Structure: News
	Module: content
	We want: The record, fields title and text

	/api.json?query=News&fields=title;text

#1.3: Specifying a module

	Structure: News (suppose there are two structures called News)
	Module: texts
	We want: Sometimes, you may want only content from a specific module,
			mainly when there are two structures with the same name (which
			you should avoid).

	/api.json?query=News&module=texts&id=10&fields=title

2) Retrieve Content with WHERE:
====

#2.1: Using WHERE

	Structure: News
	Module: content
	We want: Record where the 'title' field equals to 'New Service Offers'

	/api.json?query=News&where_title=new+service+offers

#2.2: Using multiple WHERE statements

	Structure: News
	Module: content
	We want:
		'title' equals to 'New Service Offers'
	 	'text' starts with 'public', no matter what's after
		(* means any text)

	/api.json?query=News&where_title=new+service+offers&where_text=public*

#2.3: By id using WHERE

	Structure: News
	Module: content
	We want: where the 'id' is 10

	/api.json?query=News&where_id=10

#2.4: Two possible values for the same field

	Structure: News
	Module: content
	We want:
		'title' equals to 'New Service Offers*' and 'Google*'
	Comment:
		This will generate a WHERE subquery using OR as logic, i.e.
		title LIKE '%new service offers% OR title LIKE 'google%'

	/api.json?query=News&where_title=*new+service+offers*;google*


3) Retrieve Content with from complex data models
====

#3.1: FlexFields use case: lots of images

	Structure: News
	Module: flex_fields
	We want:
		Suppose FlexFields has a fields containing many photos. 
		The photos should return as multiple keys in 'photos' field
		(one for each picture).
		
	Comment:
		This will generate a WHERE subquery using OR as logic, i.e.
		title LIKE '%new service offers% OR title LIKE 'google%'

	/api.json?query=News


4) Retrieve Content with custom module API options
====

#4.1: Retrieving the last 14 photos inserted in FlexFields 

	Structure: News
	Module: flex_fields
	We want:
		Sometimes, a peculiar feature will have to be implemented by the Module itself.
		Let's say that you need to get the last 14 photos from FlexFields, independent
		of the record. 14 last pics by date, no matter what the record.
		
		In the example below, the 'field_photos_only' is a module feature, not Aust's core.
		It should be implemented by the module itself.

	/api.json?query=News&field_photos_only&limit=14



Other Queries
====

1) Get configuration value
====

#1.1: Get configuration value by property

	We want:
		To get the value of a configuration with property named "site_title".
		
	/api.json?configuration=site_title
