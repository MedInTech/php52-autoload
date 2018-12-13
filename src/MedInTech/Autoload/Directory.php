<?php

class MedInTech_Autoload_Directory extends MedInTech_Autoload_Base implements MedInTech_Autoload_Interface
{
  protected $directories;
  protected $prefix;
  protected $recursive;

  protected $map = array();
  protected $extensions;

  public function __construct($dir, MedInTech_Autoload_Interface $next = null, $recursive = true, $extensions = array('php', 'inc'))
  {
    parent::__construct($next);

    $this->directories = is_array($dir) ? $dir : array($dir);
    $this->next = $next;
    $this->recursive = $recursive;
    $this->extensions = $extensions;
  }

  public function load($className)
  {
    if (empty($this->map)) {
      foreach ($this->directories as $directory) {
        $this->loadDirectory($directory);
      }
    }

    if (array_key_exists(strtolower($className), $this->map)) {
      $filename = $this->map[strtolower($className)];

      $this->currentData['filename'] = $filename;
      $this->currentData['path'] = reset($this->directories);

      return parent::load($className);
    }

    return $this->next && $this->next->load($className);
  }
  protected function loadDirectory($dir)
  {
    $objects = $this->recursive ? new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($dir),
      RecursiveIteratorIterator::SELF_FIRST
    ) : new DirectoryIterator($dir);

    /** @var SplFileInfo $object */
    foreach ($objects as $name => $object) {
      if (!$object->isFile()) continue;

      $extension = $this->getFileExtension($object);
      if (!in_array($extension, $this->extensions)) {
        continue;
      }
      $filename = $this->fileToClassname($object);
      if (false !== $filename) {
        $this->map[strtolower($filename)] = $name;
      }
    }
  }
  protected function getFileExtension(SplFileInfo $file)
  {
    $filename = $file->getFilename();

    foreach ($this->extensions as $extension) {
      $dotExt = '.' . $extension;
      if (strripos($filename, $dotExt, 0) === strlen($filename) - strlen($dotExt)) { // endsWith
        return $extension;
      }
    }

    return pathinfo($file->getFilename(), PATHINFO_EXTENSION);

  }
  protected function fileToClassname(SplFileInfo $file)
  {
    $filename = $file->getFilename();

    foreach ($this->extensions as $extension) {
      $dotExt = '.' . $extension;
      if (strripos($filename, $dotExt, 0) === strlen($filename) - strlen($dotExt)) { // endsWith
        return $file->getBasename($dotExt);
      }
    }

    return $file->getBasename('.' . pathinfo($file->getFilename(), PATHINFO_EXTENSION));
  }
}