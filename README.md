# WPFactory Cross-Selling
A library designed for WPFactory plugins, aimed at cross-selling by offering WPFactory product recommendations

## How to use it?
1. Create/Put the composer.json on the root folder.

2. Require the Composer `autoload.php` on main plugin file. Most of our plugins are already doing it. Example:
```php
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
```

3. Then initialize the library with `new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling()` from within the main plugin class. Probably the best place is inside the hook `plugins_loaded`. If the main class is already being loaded with that hook, you can simply load the library in the class constructor.
> [!NOTE]  
> Try to remember to only run inside a `is_admin()` check.

*Example:*

```php
add_action( 'plugins_loaded', function(){  
  $main_plugin_class = new Main_Plugin_Class();  
} );
```

```php
class Main_Plugin_Class(){

    function __construct() { 
        $this->init_cross_selling_library();
    }

    function init_cross_selling_library(){
        if ( ! is_admin() ) {
            return;
        }

        // Initializes WPFactory Key Manager library.
        $cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
        $cross_selling->setup( array(
            'plugin_file_path'   => __FILE__,
            'plugin_action_link' => array(
                'enabled' => true
              ),
        ) );
        $cross_selling->init()
    }
}
```
