#About Curatescape

Curatescape is the set of tools used in the Mobile Historical initiative, including a set of themes and plugins for the [Omeka CMS](http://omeka.org), and native client applications for Android and iOS devices.

The Mobile Historical initiative is an NEH-funded project aiming to develop best practices for curating physical landscapes and location-based digital collections using low-cost and open-source publishing tools.

For more information, visit [curatescape.org](http://curatescape.org/) or view the [GitHub project wiki](https://github.com/CPHDH/Curatescape/wiki).

#Server-side Setup

Curatescape is a suite of themes and plugins for the Omeka content management system. To get started, install Omeka on the server of your choice.

1. Install Omeka 
	- [Download Omeka (current supported version: 1.5.3)](http://omeka.org/files/omeka-1.5.3.zip) 
	- [Installation instructions (omeka.org)](http://omeka.org/codex/Installation)
	- [Server requirements](https://github.com/CPHDH/Curatescape/wiki/Server-requirements)
2. Curatescape themes 
	- Curatescape: [repo](https://github.com/CPHDH/theme-curatescape) | [download](https://github.com/CPHDH/theme-curatescape/archive/master.zip) 
	- ~~MobileHistorical~~ DEPRECATED  (for use only with Omeka 1.5.x; requires Geolocation_fork plugin): [repo](https://github.com/CPHDH/theme-MobileHistorical) | [download](https://github.com/CPHDH/theme-MobileHistorical/archive/master.zip) 
3. Curatescape plugins 
	- TourBuilder:  [repo](https://github.com/CPHDH/plugin-TourBuilder) | [download](https://github.com/CPHDH/plugin-TourBuilder/archive/master.zip)  
	- MobileJSON: [repo](https://github.com/CPHDH/plugin-MobileJson) | [download](https://github.com/CPHDH/plugin-MobileJson/archive/master.zip) 
	- ImageTree: [repo](https://github.com/CPHDH/plugin-ImageTree) | [download](https://github.com/CPHDH/plugin-ImageTree/archive/master.zip)  
	- ~~Geolocation_fork~~ DEPRECATED  (for use only with Omeka 1.5.x and Mobile Historical theme only): [repo](https://github.com/CPHDH/plugin-geolocation_fork) | [download](https://github.com/CPHDH/plugin-geolocation_fork/archive/master.zip) 
	- SendToAdminHeader: [repo](https://github.com/CPHDH/plugin-SendToAdminHeader) | [download](https://github.com/CPHDH/plugin-SendToAdminHeader/archive/master.zip) 
4. CHNM plugins
	- Geolocation: [instructions](http://omeka.org/codex/Plugins/Geolocation) | [repo](https://github.com/omeka/plugin-Geolocation) | [download v. 1.2](http://omeka.org/wordpress/wp-content/uploads/2011/07/Geolocation-1.3-1.2.zip) (for Omeka 1.5.3 and Curatescape theme only)

_Note that you can quickly grab one of the colocated Omeka+Curatescape server-side packages by cloning this repo or by using a shell command like:_
```
wget https://github.com/CPHDH/Curatescape/archive/master.zip 
```

#Native client apps

Note that while the server-side components of Curatescape are open source and freely available, the native client applications are proprietary and require a licensee fee. For more information about licensing, visit [curatescape.org](http://curatescape.org/).

#Other documentation
Please visit the [Curatescape project wiki](https://github.com/CPHDH/Curatescape/wiki) for additional information regarding [configuration](https://github.com/CPHDH/Curatescape/wiki/Configuring-omeka-for-curatescape), [content framework](https://github.com/CPHDH/Curatescape/wiki/Conceptual-and-organizational-framework) and more.