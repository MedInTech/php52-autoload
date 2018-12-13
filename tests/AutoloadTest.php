<?php

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AutoloadTest extends \PHPUnit\Framework\TestCase
{
  public function testSimpleSnake()
  {
    $this->assertFalse(class_exists('Some_Prefix_A_B_Snake1', false));

    $autoload = new MedInTech_Autoload_Snake(__DIR__ . '/classes/Snake1', null, 'Some_Prefix');
    $autoload->register();

    $this->assertTrue(class_exists('Some_Prefix_A_B_Snake1'));

    $c = new Some_Prefix_A_B_Snake1();
    $this->assertEquals('Some_Prefix_A_B_Snake1', get_class($c));
  }

  public function testChainedSnake()
  {
    $this->assertFalse(class_exists('Some_Prefix_A_B_Snake2', false));

    $autoload = new MedInTech_Autoload_Snake(__DIR__ . '/classes/NoExists', null);
    $autoload = new MedInTech_Autoload_Snake(__DIR__ . '/classes/Snake1', $autoload, 'Wrong_Prefix');
    $autoload = new MedInTech_Autoload_Snake(__DIR__ . '/classes/Snake1', $autoload, 'Some_Prefix');
    $autoload->register();

    $this->assertTrue(class_exists('Some_Prefix_A_B_Snake2'));

    $c = new Some_Prefix_A_B_Snake2();
    $this->assertEquals('Some_Prefix_A_B_Snake2', get_class($c));
  }

  public function testDirectory()
  {
    $this->assertFalse(class_exists('Directory1', false));

    $autoload = new MedInTech_Autoload_Directory(__DIR__ . '/classes');
    $autoload->register();

    $this->assertTrue(class_exists('Directory1'));

    $c = new Directory1();
    $this->assertEquals('Directory1', get_class($c));
  }
  public function testDirectoryCustomExt()
  {
    $this->assertFalse(class_exists('Directory2', false));

    $autoload = new MedInTech_Autoload_Directory(__DIR__ . '/classes', null, true, array('class.php'));
    $autoload->register();

    $this->assertTrue(class_exists('Directory2'));

    $c = new Directory2();
    $this->assertEquals('Directory2', get_class($c));
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