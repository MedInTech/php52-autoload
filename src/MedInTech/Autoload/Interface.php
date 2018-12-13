<?php

interface MedInTech_Autoload_Interface
{
  public function register();
  public function addPlugin(MedInTech_Autoload_IPlugin $plugin);

  public function load($className);
}
