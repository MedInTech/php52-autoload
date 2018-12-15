<?php

require_once dirname(__FILE__) . '/Interface.php';
require_once dirname(__FILE__) . '/IPlugin.php';

class MedInTech_Autoload_Base implements MedInTech_Autoload_Interface
{
  /** @var MedInTech_Autoload_IPlugin[] */
  protected $plugins = array();
  protected $currentData = array();
  /** @var MedInTech_Autoload_Interface */
  protected $next;

  public function __construct(MedInTech_Autoload_Interface $next = null)
  {
    $this->next = $next;
  }

  public function load($className)
  {
    $filename = isset($this->currentData['filename']) ? $this->currentData['filename'] : null;
    $path = isset($this->currentData['path']) ? $this->currentData['path'] : null;

    foreach ($this->plugins as $plugin) {
      $param = $plugin->process($className, $filename, $path);
      if ($param === true) return true;
    }
    /** @noinspection PhpIncludeInspection */
    require_once $filename;
    if (self::isLoaded($className)) return true;

    return $this->next ? $this->next->load($className) : null;
  }
  public function register()
  {
    spl_autoload_register(array($this, 'load'));
  }
  public function addPlugin(MedInTech_Autoload_IPlugin $plugin)
  {
    array_unshift($this->plugins, $plugin);
    if ($this->next) $this->next->addPlugin($plugin);
  }
  public static function isLoaded($className)
  {
    return class_exists($className) || interface_exists($className);
  }
}
