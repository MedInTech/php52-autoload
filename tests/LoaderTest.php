<?php

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LoaderTest extends \PHPUnit\Framework\TestCase
{
  public function testFlow()
  {
    $loader = new MedInTech_Autoload_Loader(array(
      'chain' => array(
        array('type' => 'Snake', 'base' => '/classes/NoExists'),
        array('type' => 'Snake', 'base' => '/classes/Snake1', 'prefix' => 'Wrong_Prefix'),
        array('type' => 'Snake', 'base' => '/classes/Snake1', 'prefix' => 'Some_Prefix'),
        array('type' => 'Directory', 'base' => '/classes'),
        array('type' => 'Directory', 'base' => '/classes', 'extensions' => array('class.php')),
      ),
    ), __DIR__);

    $this->assertFalse(class_exists('Some_Prefix_A_B_Snake1'));
    $this->assertFalse(class_exists('Some_Prefix_A_B_Snake2'));
    $this->assertFalse(class_exists('Directory1'));
    $this->assertFalse(class_exists('Directory2'));
    $loader->register();
    $this->assertTrue(class_exists('Some_Prefix_A_B_Snake1'));
    $this->assertTrue(class_exists('Some_Prefix_A_B_Snake2'));
    $this->assertTrue(class_exists('Directory1'));
    $this->assertTrue(class_exists('Directory2'));
  }

  private $alCnt;
  protected function setUp()
  {
    $this->alCnt = count(spl_autoload_functions());
  }
  protected function tearDown()
  {
    $functions = spl_autoload_functions();
    foreach ($functions as $i => $function) {
      if ($i < $this->alCnt) continue; // skip foreign(composer) autoloads
      spl_autoload_unregister($function);
    }
  }
}