<?php

require_once dirname(__FILE__) . '/Interface.php';
require_once dirname(__FILE__) . '/IPlugin.php';
require_once dirname(__FILE__) . '/Snake.php';
require_once dirname(__FILE__) . '/Directory.php';

class MedInTech_Autoload_Loader implements MedInTech_Autoload_Interface
{
  private $rules;
  private $rootDir;
  public function __construct($rules, $rootDir = null)
  {
    $this->rules = $rules;
    if ($rootDir) {
      $this->rootDir = $rootDir;
    } else { // vendor escape
      $vendorDir = dirname(__FILE__) . '/../../..';
      $this->rootDir = realpath("$vendorDir/..");
    }
  }
  public function load($className)
  {
    $autoload = $this->build();

    return $autoload->load($className);
  }
  public function register()
  {
    $autoload = $this->build();
    $autoload->register();
  }
  public function addPlugin(MedInTech_Autoload_IPlugin $plugin)
  {
    $autoload = $this->build();
    $autoload->addPlugin($plugin);
  }

  private $autoloader;
  protected function build()
  {
    if ($this->autoloader) return $this->autoloader;
    $chain = $this->rules['chain'];
    $autoload = null;
    foreach ($chain as $item) {
      $base = !empty($item['base']) ? $item['base'] : '';
      $dir = "{$this->rootDir}{$base}";
      switch ($item['type']) {
        case 'Snake':
          $prefix = !empty($item['prefix']) ? $item['prefix'] : null;
          $autoload = new MedInTech_Autoload_Snake($dir, $autoload, $prefix);
          break;
        case 'Directory':
          $recursive = !empty($item['recursive']) ? $item['recursive'] : true;
          $extensions = !empty($item['extensions']) ? $item['extensions'] : array('php', 'inc');
          $autoload = new MedInTech_Autoload_Directory($dir, $autoload, $recursive, $extensions);
          break;
      }
    }

    return $autoload;
  }
}