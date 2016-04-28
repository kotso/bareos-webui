<?php

/**
 *
 * bareos-webui - Bareos Web-Frontend
 *
 * @link      https://github.com/bareos/bareos-webui for the canonical source repository
 * @copyright Copyright (c) 2013-2015 Bareos GmbH & Co. KG (http://www.bareos.org/)
 * @license   GNU Affero General Public License (http://www.gnu.org/licenses/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Auth\Form;

use Zend\Form\Form;

class LoginForm extends Form
{

   protected $config;
   protected $directors;
   protected $availableLocales;
   protected $locale;

   public function __construct($config=null, $name=null)
   {

      $this->config = $config;
      $this->directors = $this->getDirectors();
      $this->availableLocales = $this->getAvailableLocales();
      $this->locale = $this->determineLanguage();

      parent::__construct('login');

      if(count($this->directors) == 1) {
         $this->add(array(
                  'name' => 'director',
                  'type' => 'select',
                  'options' => array(
                     'label' => 'Director',
                     'empty_option' => 'Please choose a director',
                     'value_options' => $this->directors,
                  ),
                  'attributes' => array(
                     'id' => 'director',
                     'value' => key($this->directors)
                  )
               )
         );
      }
      else {
         $this->add(array(
                  'name' => 'director',
                  'type' => 'select',
                  'options' => array(
                     'label' => 'Director',
                     'empty_option' => 'Please choose a director',
                     'value_options' => $this->directors,
                  ),
                  'attributes' => array(
                     'id' => 'director',
                  )
               )
         );
      }

      if(isset($this->locale)) {
         $this->add(array(
            'name' => 'locale',
            'type' => 'select',
            'options' => array(
               'label' => 'Language',
               'empty_option' => 'Please choose a language',
               'value_options' => $this->availableLocales
            ),
            'attributes' => array(
               'id' => 'locale',
               'value' => key($this->locale)
            )
         ));
      }
      else {
         $this->add(array(
            'name' => 'locale',
            'type' => 'select',
            'options' => array(
               'label' => 'Language',
               'empty_option' => 'Please choose a language',
               'value_options' => $this->availableLocales
            ),
            'attributes' => array(
               'id' => 'locale',
            )
         ));
      }

      $this->add(array(
               'name' => 'consolename',
               'type' => 'text',
               'options' => array(
                  'label' => 'Username',
               ),
               'attributes' => array(
                  'placeholder' => 'Username',
               ),
            )
      );

      $this->add(array(
               'name' => 'password',
               'type' => 'password',
               'options' => array(
                  'label' => 'Password',
               ),
               'attributes' => array(
                  'placeholder' => 'Password',
               ),
            )
      );

      $this->add(array(
         'name' => 'submit',
         'type' => 'submit',
         'attributes' => array(
            'value' => 'Login',
            'id' => 'submit',
         ),
      )
      );

   }

   private function determineLanguage($default = "en_EN") {

      $l = array();

      if(!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) || empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
         $l['en_EN'] = 'English';
         return $l;
      }

      $accepted = preg_split("{,\s*}", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
      $language = $default;
      $quality = 0;

      if(is_array($accepted) && (count($accepted) > 0)) {
         foreach($accepted as $key => $value) {
            if(!preg_match("{^([a-z]{1,8}(?:-[a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$}i", $value, $matches)) {
                continue;
            }

            $code = explode("-", $matches[1]);

            if(isset($matches[2])) {
                $priority = floatval($matches[2]);
            } else {
                $priority = 1.0;
            }

            while(count($code) > 0) {
                if($priority > $quality) {
                    $language = implode("_", $code);
                    $quality = $priority;
                    break;
                }
                break;
            }
         }
      }

      switch($language) {
         case 'de_DE':
            $l['de_DE'] = 'German';
            break;
         case 'de':
            $l['de_DE'] = 'German';
            break;
         case 'en_EN':
            $l['en_EN'] = 'English';
            break;
         case 'en_GB':
            $l['en_EN'] = 'English';
            break;
         case 'en_US':
            $l['en_EN'] = 'English';
            break;
         case 'en':
            $l['en_EN'] = 'English';
            break;
         case 'fr':
            $l['fr_FR'] = 'French';
            break;
         case 'fr_FR':
            $l['fr_FR'] = 'French';
            break;
         case 'ru_RU':
            $l['ru_RU'] = 'Russian';
            break;
         case 'ru':
            $l['ru_RU'] = 'Russian';
            break;
         default:
            $l = null;
      }

      return $l;
   }

   private function getAvailableLocales()
   {
      $locales = array();

      $locales['en_EN'] = "English";
      $locales['fr_FR'] = "French";
      $locales['de_DE'] = "German";
      $locales['ru_RU'] = "Russian";

      return $locales;
   }

   public function getDirectors()
   {
      $selectData = array();

      foreach($this->config as $dird) {
         $selectData[key($this->config)] = key($this->config);
         next($this->config);
      }

      return $selectData;
   }

}
