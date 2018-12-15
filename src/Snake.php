<?php

require_once dirname(__FILE__) . '/Interface.php';
require_once dirname(__FILE__) . '/Base.php';

class MedInTech_Autoload_Snake extends MedInTech_Autoload_Base implements MedInTech_Autoload_Interface
{
  private $baseDir;
  private $prefix;
  public function __construct($dir, MedInTech_Autoload_Interface $next = null, $prefix = null)
  {
    parent::__construct($next);

    $this->baseDir = $dir;
    $this->prefix = $prefix;
    $this->next = $next;
  }

  public function load($className)
  {
    if (!empty($this->prefix) && !preg_match("/^{$this->prefix}_/i", $className)) {
      // skip classes with wrong prefix
      return $this->next && $this->next->load($className) ? true : null;
    }
    $class = $this->prefix ?
      preg_replace("/^{$this->prefix}_/i", '', $className) :
      $className;
    $parts = explode('_', $class);
    $classPath = array($this->baseDir);
    foreach ($parts as $part) {
      $classPath[] = ucfirst($part);
    }
    $fileName = implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
    // Harcode for restore filename case, if classname in wrong(Some engines do lowercase them)
    if (!file_exists($fileName)) {
      $bdShell = escapeshellarg($this->baseDir);
      $fnShell = escapeshellarg(strtolower($fileName));
      $findCmd = "find $bdShell -ipath $fnShell";
      exec($findCmd, $list);
      $fileName = reset($list);
    }

    if (is_file($fileName)) {
      $this->currentData['filename'] = $fileName;
      $this->currentData['path'] = $this->baseDir;

      return parent::load($className);
    }

    return $this->next && $this->next->load($className);
  }
}