# Linkit plugin for Craft

One link field to replace them all, a multi-purpose link fieldtype for Craft CMS.

This plugin adds a fieldtype which links to all sorts of stuff, Linkit can currently link to:

* Entries
* Assets
* Categories
* Emails
* Phone numbers
* Custom URLs

And it plays really nicely with Matrix!

Field settings allow you to:

* Configure what elements each field can link too.
* Set which entry/asset sources are available to each field.
* Allow fields to set custom link text.
* Allow fields to set links to open in new window.
* Set default link text.


## Installation

To install Linkit, follow these steps:

1.  Upload the linkit/ folder to your craft/plugins/ folder.
2.  Go to Settings > Plugins from your Craft control panel and enable the Linkit plugin.
3.  Create and configure a new linkit field.

## Template Usage

Get the link object:

	{% set link = entry.yourLinkFieldHandle %}
	
Just the link - output a pre built HTML link:

	{{ link.link|raw }}
			
Build a simple custom link:

	<a href="{{ link.url }}" {{ link.target ? ' target="_blank"' }} title="{{ link.linkText }}">{{ link.linkText }}</a>
			
Build your own:			
 
	{% switch link.type %}
	
	    {% case "entry" %}
	    
			<a href="{{ link.entry.url }}" {{ link.target ? ' target="_blank"' }} title="{{ link.entry.title }}">{{ link.entry.title }}</a>
			
	    {% case "category" %}
	    
			<a href="{{ link.category.url }}" {{ link.target ? ' target="_blank"' }} title="{{ link.category.title }}">{{ link.category.title }}</a>

	    {% case "asset" %}
						    
			<a href="{{ link.asset.url }}" {{ link.target ? ' target="_blank"' }} title="{{ link.asset.title }}">
				{% if link.asset.kind == 'image' %}
					<img src="{{ link.asset.url('thumb') }}" />
				{% else %}
					<img src="thumb-{{ link.asset.kind }}.png" />
				{% endif %}
			</a>		
	
	    {% case "custom" %}
	    
			<a href="{{ link.custom }}" {{ link.target ? ' target="_blank"' }} title="{{ link.linkText }}">
				<i class="custom-link-icon"></i> {{ link.linkText }}
			</a>
			
	    {% case "email" %}
	    
			<a href="mailto:{{ link.email }}" {{ link.target ? ' target="_blank"' }} title="{{ link.linkText }}">{{ link.email }}</a></p>
	
	    {% case "tel" %}
	    
			<a href="tel:{{ link.tel }}" {{ link.target ? ' target="_blank"' }} title="{{ link.linkText }}">{{ link.tel }}</a></p>
	
	{% endswitch %}
	
## Template Variables

Each link returns a link object which contains the following:

	{% set link = entry.yourLinkFieldHandle %} // Get the link

	{{ link.type }} // Returns the link type - entry, asset, email, tel, custom

	{{ link.email }}  	// Email String / False
	{{ link.custom }} 	// Custom URL String / False
	{{ link.tel }}		// Telephone Number / False
	{{ link.entry }}	// Entry Object / False
	{{ link.category }}	// Category Object / False
	{{ link.asset }}	// Asset Object / False
	// Each link type is returned - only the active type will return data the rest return false

	{{ link.text }} 	// The Custom Text String 
	{{ link.target }}   // True/False (Bool) - Open in new window? 
	
	{{ link.url }}		// The full url (correct prefix added eg mailto: or tel:)
	{{ link.linkText }} // The link text string ready to use (If no custom text is provided it generates it based on the link type)
	
	{{ link.link }}		// Full link HTML ready to use	

## Roadmap

* Force download option
* Rework the way link data is returned
* Improve handling of target stuff - if it's required


## Changelog

### 0.9

* Added: Removed the requirement to use the |raw filter when using the link variable.
* Fixed: Input field now corectly displays when on one link type is setup.

### 0.8

### 0.8.1

* Added: Hide the Link To... select when only one link type has been selected for the field.

### 0.8

* Added: Category Support

### 0.7

* Added: Default Text - Fieldtype setting to add default link text for a link.

### 0.6

* Fix: PHP Error when returning an entry or asset that has subsequently been deleted

### 0.5

* Initial beta release

## Licence

Copyright 2014 Fruit Studios Ltd
