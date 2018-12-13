<?php

interface MedInTech_Autoload_IPlugin
{
  /**
   * @param      $classname
   * @param null $filename
   * @param null $path
   *
   * @return boolean
   *                true - class load finished, stop processing
   */
  public function process($classname, $filename = null, $path = null);
}