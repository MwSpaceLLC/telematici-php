<?php namespace Ethical\Telematici;

/**
 *  Copyright © 2019 MwSpace s.r.l <https://mwspace.com>
 *
 *  Developers (C) 2019 Aleksandr Ivanovitch <https://www.facebook.com/Aleksandr.Ivanovitch.Brunelli/>
 *
 *  This file is part of telematici-php.
 *
 * You should have received a copy of the MIT License
 * along with telematici-php.  If not, see <https://it.wikipedia.org/wiki/Licenza_MIT/>.
 *
 * To work without composer autoload, include manually all class
 *
 *   ______     ___      __    __  .___________. __    ______   .__   __.
 *  /      |   /   \    |  |  |  | |           ||  |  /  __  \  |  \ |  |
 * |  ,----'  /  ^  \   |  |  |  | `---|  |----`|  | |  |  |  | |   \|  |
 * |  |      /  /_\  \  |  |  |  |     |  |     |  | |  |  |  | |  . `  |
 * |  `----./  _____  \ |  `--'  |     |  |     |  | |  `--'  | |  |\   |
 *  \______/__/     \__\ \______/      |__|     |__|  \______/  |__| \__|
 *
 * THIS LIBRARY NOT LEGAL
 * THIS IS ONLY FOR EXPERIMENTAL POWER THAT AI CAN BE
 *
 * NOT USE FOR PRODUCTION
 * NOT USE FOR COMMERCIAL USE
 *
 */

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Monolog\Logger;

/**
 * Class Telematici
 * @package Ethical\Telematici
 */
final class Service
{
    /**
     * @var bool
     */
    private $telematici;

    /**
     * Telematici constructor.
     * @param $filepath
     * @throws \Exception
     */
    public final function __construct($filepath)
    {

        if (!is_file($filepath)) {
            throw  new \Exception("non-compliant file at $filepath");
        }

        if (!is_readable($filepath)) {
            throw  new \Exception("unreadable file at $filepath");
        }

        if (!file_exists($filepath)) {
            throw  new \Exception("file not found at $filepath");
        }

        /**
         * Autentication Api File
         */
        putenv("GOOGLE_APPLICATION_CREDENTIALS=$filepath");
        $this->telematici = 'https://telematici.agenziaentrate.gov.it/';

    }

    /**
     * @param $fiscalcode
     * @return mixed
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public final function validCF($fiscalcode)
    {
        if (!isset($fiscalcode)) {
            throw  new \Exception("Fiscal Code not set");
        }

        if (!ctype_alnum($fiscalcode)) {
            throw  new \Exception("Fiscal Code parse not correct string at: $fiscalcode");
        }

        /**
         * Start while to force Google ml to find captcha
         */
        $find = 0;
        while ($find < 1) {

            $imageAnnotator = new ImageAnnotatorClient();

            /**
             * Start first curl for autenticate client
             */

            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $this->telematici,
                // You can set any number of default request options.
                'timeout' => 5.0,
                'cookies' => true
            ]);

            /**
             * Init first call for retrive captcha and set coockies 'JSESSIONID'
             */
            $response = $client->request('GET', 'VerificaCF/captcha');

            /**
             * Client have found the image_captcha
             */
            $image = $response->getBody()->getContents();
            $response = $imageAnnotator->documentTextDetection($image);
            $annotation = $response->getFullTextAnnotation();

            /**
             * Increase Log Cycles
             */
            $log = new Logger('cycleOfVisionRequestForCf');
            $log->info('+1');

            # print out detailed and structured information about document text
            if ($annotation) {

                foreach ($annotation->getPages() as $page) {
                    foreach ($page->getBlocks() as $block) {
                        $captcha = '';
                        foreach ($block->getParagraphs() as $paragraph) {
                            foreach ($paragraph->getWords() as $word) {
                                foreach ($word->getSymbols() as $symbol) {
                                    $captcha .= $symbol->getText();
                                }
                            }
                        }

                        /**
                         * Try to Init request with same client & captcha $captcha
                         */
                        $response = $client->request('POST', 'VerificaCF/VerificaCf.do', array(
                            'form_params' => array(
                                'cf' => strtoupper($fiscalcode),
                                'inCaptchaChars' => $captcha
                            )
                        ));

                        /**
                         * Try to check result Construct Document
                         */
                        $doc = new \DOMDocument();

                        libxml_use_internal_errors(true);
                        $doc->loadHTML($response->getBody()->getContents());
                        libxml_use_internal_errors(false);

                        $contentFindOut = strip_tags($doc->getElementById('vcfcontenitore')->textContent);

                        if (strpos($contentFindOut, 'CODICE FISCALE NON VALIDO') !== false) {
                            /** Stop while loop */
                            $find = 1;

                            /**
                             * Fiscal Not Found
                             */
                            return false;
                        }

                        if (strpos($contentFindOut, 'CODICE FISCALE VALIDO') !== false) {
                            /** Stop while loop */
                            $find = 1;

                            /**
                             * Fiscal Found
                             */
                            return true;
                        }
                    }
                }
            } else {
                /**
                 * Captcha not have result. Google ml try again
                 */
                $find = 0;
            }

            $imageAnnotator->close();
        }
    }

    /**
     * @param $vatnumber
     * @return bool
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public final function validPIVA($vatnumber)
    {

        if (!isset($vatnumber)) {
            throw  new \Exception("Vat Number not set");
        }

        if (!is_numeric($vatnumber)) {
            throw  new \Exception("Vat Number parse not correct Integer at: $fiscalcode");
        }

        /**
         * Start while to force Google ml to find captcha
         */
        $find = 0;
        while ($find < 1) {

            $imageAnnotator = new ImageAnnotatorClient();

            /**
             * Start first curl for autenticate client
             */

            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $this->telematici,
                // You can set any number of default request options.
                'timeout' => 5.0,
                'cookies' => true
            ]);

            /**
             * Init first call for retrive captcha and set coockies 'JSESSIONID'
             */
            $response = $client->request('GET', 'VerificaPIVA/captcha');

            /**
             * Client have found the image_captcha
             */
            $image = $response->getBody()->getContents();
            $response = $imageAnnotator->documentTextDetection($image);
            $annotation = $response->getFullTextAnnotation();

            /**
             * Increase Log Cycles
             */
            $log = new Logger('cycleOfVisionRequestForVat');
            $log->info('+1');

            # print out detailed and structured information about document text
            if ($annotation) {

                foreach ($annotation->getPages() as $page) {
                    foreach ($page->getBlocks() as $block) {
                        $captcha = '';
                        foreach ($block->getParagraphs() as $paragraph) {
                            foreach ($paragraph->getWords() as $word) {
                                foreach ($word->getSymbols() as $symbol) {
                                    $captcha .= $symbol->getText();
                                }
                            }
                        }

                        /**
                         * Try to Init request with same client & captcha $captcha
                         */
                        $response = $client->request('POST', 'VerificaPIVA/VerificaPiva.do', array(
                            'form_params' => array(
                                'piva' => $this->request->pi,
                                'inCaptchaChars' => $captcha
                            )
                        ));

                        /**
                         * Try to check result Construct Document
                         */
                        $doc = new \DOMDocument();

                        libxml_use_internal_errors(true);
                        $doc->loadHTML($response->getBody()->getContents());
                        libxml_use_internal_errors(false);

                        $contentFindOut = strip_tags($doc->getElementById('vcfcontenitore')->textContent);

                        if (strpos($contentFindOut, 'PARTITA IVA NON VALIDA') !== false) {
                            /** Stop while loop */
                            $find = 1;

                            /**
                             * Fiscal Not Found
                             */
                            return false;

                        }

                        if (strpos($contentFindOut, 'PARTITA IVA ATTIVA') !== false) {
                            /** Stop while loop */
                            $find = 1;

                            /**
                             * Vat Number Found
                             */
                            return true;

                        }
                    }
                }
            } else {
                /**
                 * Captcha not have result. Google ml try again
                 */
                $find = 0;
            }

            $imageAnnotator->close();
        }

    }
}