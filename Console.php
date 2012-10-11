<?php
/**
 * Simple console for PHP CLI
 * 
 * @author <contact@michel.dourneau.fr>
 *         http://www.michel-dourneau.fr/
 * 
 * @uses echo Console::message('<notice>Message</notice>');
 * @uses $reponse = Console::question('Wath, <success>Choice1</success> or <success>Choice1</success>');
 *  
 * @todo Add backgroud color and regular space
 */
abstract class Console
{
  /**
   * Display hour
   * 
   * @var bool $displayHour
   */
  static public $displayHour = true;

  /**
   * Display memory usage
   * 
   * @var bool $displayMemoryUsage
   */
  static public $displayMemoryUsage = true;
  
  /**
   * Number of digits after the decimal point
   * 
   * @var int $memoryUsageDecimal
   */
  static public $memoryUsageDecimal = 2;
  
  /**
   * Format memory output
   * 
   * @var string
   */
  static public $formatOutPutMemory = "[<alert>%sMo</alert>]";
  
  /**
   * Format hour output
   * 
   * @var string
   */
  static public $formatOutPutHour = "[%s]";  
  
  /**
   * Colors test list
   * 
   * @todo add more
   * @var array $textColor
   */
  static public $textColor = array(
      'green'   => '32',
      'magenta' => '35',
      'blue'    => '34',
      'red'     => '31',
      'yellow'  => '33',
  );

  /**
   * Color background
   * 
   * @todo add more
   * @var array $tagColor
   */
  static public $tagColor = array(
      'notice'  => 'blue',
      'info'    => 'yellow',
      'alert'   => 'red',
      'success' => 'green',
  );


  /**
   * Return message with colors.
   * 
   * @param string $message 
   */
  static public function message($message) {
    $message = self::parseCallBackColor($message);
    $output  = self::formatOutPut($message);
    
    return sprintf("%s\n", $output);
  }
  
   /**
    * <Echo> one of the message and returns the response of the user in 
    * question posed $ message.
    *
    * @param  string $message
    * @return string
    */
  static public function question($message) {
      $message = self::parseCallBackColor($message);

      echo self::formatOutPut($message.": ");
      fscanf(STDIN, "%s", $response);

      return (string) trim($response);
  }  

  /**
   * Parse a string, marker tags and adds color.
   * 
   * @param  string $string
   * @return string
   */
  static public function parseCallBackColor($string) {
    $string = preg_replace_callback('|<success[^>]*>(.*)<\/success>|U',
        create_function('$matches','return Console::setColorString($matches[1], "success");'),
        $string
    );

    $string = preg_replace_callback('|<info[^>]*>(.*)<\/info>|U',
        create_function('$matches','return Console::setColorString($matches[1], "info");'),
        $string
    );

    $string = preg_replace_callback('|<notice[^>]*>(.*)<\/notice>|U',
        create_function('$matches','return Console::setColorString($matches[1], "notice");'),
        $string
    );

    $string = preg_replace_callback('|<alert[^>]*>(.*)<\/alert>|U',
        create_function('$matches','return Console::setColorString($matches[1], "alert");'),
        $string
    );
    
    return (string) $string;
  }

  /**
   * Adding color about a text
   * 
   * @param  type $string
   * @param  type $tag
   * @return string
   */
  static public function setColorString($string, $tag=null) {
    if (null!=$tag) {
        return sprintf("\033[%sm%s\033[0m", self::$textColor[self::$tagColor[$tag]], $string);
    }

    return (string) $string;    
  }
  
  /**
   * Formats the message out.
   *
   * @param  string $string
   * @return string
   */
  static public function formatOutPut($string) {
    $memoryUsage = null;
    $hour        = null;
    
    if (self::$displayMemoryUsage) {
      $memoryUsage = self::parseCallBackColor(self::$formatOutPutMemory);
      $memoryUsage = sprintf($memoryUsage, self::getMemoryUsage());
    }

    if (self::$displayHour) {
      $hour = self::parseCallBackColor(self::$formatOutPutHour);
      $hour = sprintf($hour, date('H:i:s'));
    }
    
    return sprintf("%s%s%s", $hour, $memoryUsage, $string);
  }
  
  /**
   * Return the memory used
   *
   * @return float
   */
  static public function getMemoryUsage() {
      return (float) number_format((memory_get_usage()/1048576), self::$memoryUsageDecimal, '.', '');
  }
}