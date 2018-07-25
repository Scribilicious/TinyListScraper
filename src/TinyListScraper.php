<?php
/*
 * (c) Jens Eldering <jens@atecmedia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyListScraper;

/**
 * The Awesome Scraper
 */
class TinyListScraper {
    private $oXpath = null;
    public $aResults = array();
    public $sPathCache = false;

    /**
     * The Construct
     * @param boolean $bCache       Set false if you don't want to use caching
     * @param integer $iRefreshRate the minutes of cache time
     * @param integer $iRandom      ads a random factor to the cache clearing
     */
    function __construct($bCache = true, $iRefreshRate = 60, $iRandom = 20) {
        set_time_limit(0);
        $this->sPathSource = dirname(__FILE__) . '/';

        // Does the caching
        if ($bCache) {
            $this->iRefreshRate = $iRefreshRate;
            $this->sPathCache = sys_get_temp_dir() . '/tinylistscraper/';

            if (!file_exists($this->sPathCache)) {
                mkdir($this->sPathCache, 0777, true);

            } elseif (!$iRandom || mt_rand(0, $iRandom) === 0) {
                $this->clearCache();
            }
        }
    }

    /**
     * Get some search results
     * @param  string  $sSearch   the string to search
     * @param  array   $aSources  the site keys to search
     * @param  boolean $bSortDate sort by date
     * @return array              results
     */
    public function get($sSearch, $aScrapers = false, $bSortDate = true) {
        if (!$aScrapers) {
            $aScrapers = $this->getScrapers();
        }

        $sTempFile = md5(implode('', $aScrapers) . $sSearch);
        $aResults = $this->loadCache($sTempFile);

        if (!$aResults) {
            $aResults = array();
            foreach ($aScrapers as $sScraper) {
                if ($aResult = $this->scrape($sScraper, $sSearch)) {
                    foreach ($aResult as $iKey => $aValue) {
                        if (
                            $this->inSubArray($aResults, 'title', $aValue['title']) ||
                            $this->inSubArray($aResults, 'text', $aValue['text'])
                        ) {
                            unset($aResult[$iKey]);
                        }
                    }
                    $aResults = array_merge($aResults, $aResult);
                }
            }

            $this->saveCache($sTempFile, $aResults);
        }

        if ($bSortDate && !empty($aResults)) {
            usort($aResults, function($a, $b) {
                return $b["timestamp"] - $a["timestamp"];
            });
        }

        $this->aResults = $aResults;
        return $aResults;
    }

    /**
     * Get the list of all the scrapers
     * @return array array of scrapers
     */
    public function getScrapers() {
        $aScrapers = array();

        $aFiles = glob($this->sPathSource . 'scrapers/*.inc');
        foreach ($aFiles as $sFile) {
            $aScrapers[] = basename($sFile, '.inc');
        }

        return $aScrapers;
    }

    private function scrape($sScraper, $sSearch) {
        $sTempFile = md5($sScraper . $sSearch);
        if ($aCachedData = $this->loadCache($sTempFile)) {
            return $aCachedData;
        }

        $aResults = array();

        if (!file_exists($this->sPathSource . 'scrapers/' . $sScraper . '.inc')) {
            throw new Exception('Scraper ' . $sScraper . ' does not exists.');
        }

        include $this->sPathSource . 'scrapers/' . $sScraper . '.inc';

        if ($oDOMXPath = $this->loadDOMXPath(str_replace('{$SEARCH}', urlencode($sSearch), $aSettings['url']))) {
            $oNodes = $this->getValue($oDOMXPath, null, $aSettings['list']['container'], false);
            $sLastLink = false;

            if ($oNodes->length > 0) {
                foreach ($oNodes as $oNode) {
                    $sText = '';
                    $aResult = array(
                        'id' => $sScraper,
                        'title' => !empty($aSettings['list']['title']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['title']) : '',
                        'author' => !empty($aSettings['list']['author']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['author']) : '',
                        'tags' => !empty($aSettings['list']['tags']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['tags']) : '',
                        'source' => $aSettings['source'],
                        'date' => !empty($aSettings['list']['date']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['date']) : '',
                        'description' => !empty($aSettings['list']['description']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['description']) : '',
                        'text' => !empty($aSettings['list']['text']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['text']) : '',
                        'image' => !empty($aSettings['list']['image']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['image']) : '',
                        'thumb' => !empty($aSettings['list']['thumb']) ? $this->getValue($oDOMXPath, $oNode, $aSettings['list']['thumb']) : '',
                        'link' => $this->getValue($oDOMXPath, $oNode, $aSettings['list']['link']),
                    );

                    // Skip double content
                    if ($sLastLink === $aResult['link'] || strpos($aResult['link'], 'http') !== 0) {
                        continue;
                    }

                    $sLastLink = $aResult['link'];

                    if (!empty($aSettings['content']['text']) && $aResult['link'] && $oDOMXPathPage = $this->loadDOMXPath($aResult['link'])) {

                        // Gets the text
                        $oContent = $this->getValue($oDOMXPathPage, null, $aSettings['content']['text'], false);
                        if ($oContent->length > 0) {
                            foreach ($oContent as $oParagraph) {
                                $sText = trim(strip_tags($oParagraph->nodeValue));
                                if ($sText) {
                                    break;
                                }
                            }
                        }

                        // Gets the title
                        if (!empty($aSettings['content']['title'])) {
                            $aResult['title'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['title']);
                        }

                        // Gets the author
                        if (!empty($aSettings['content']['author'])) {
                            $aResult['author'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['author']);
                        }

                        // Gets the date
                        if (!empty($aSettings['content']['date'])) {
                            $aResult['date'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['date']);
                        }

                        // Gets the description
                        if (!empty($aSettings['content']['description'])) {
                            $aResult['description'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['description']);
                        }

                        // Gets the keywords
                        if (!empty($aSettings['content']['tags'])) {
                            $aResult['tags'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['tags']);
                        }

                        // Gets the image
                        if (!empty($aSettings['content']['image'])) {
                            $aResult['image'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['image']);
                        }

                        // Gets the thumb
                        if (!empty($aSettings['content']['thumb'])) {
                            $aResult['thumb'] = $this->getValue($oDOMXPathPage, null, $aSettings['content']['thumb']);
                        }
                    }

                    $aResult['text'] = $sText;
                    $aResult['timestamp'] = strtotime($aResult['date']);

                    $aResults[] = $aResult;
                }
            }

            $this->saveCache($sTempFile, $aResults);
        }

        return $aResults;
    }

    /**
     * Loads a DOMXPath by URL
     * @param  string $sUrl the URL
     * @return object       the DOMXPath object
     */
    private function loadDOMXPath($sUrl) {
        $sHtml = file_get_contents($sUrl, false);

        if (empty($sHtml)) {
            return false;
        }

        $oDocument = new \DOMDocument();
        libxml_use_internal_errors(TRUE); //disable libxml errors
        $oDocument->loadHTML($sHtml);
        libxml_clear_errors();
        return new \DOMXPath($oDocument);
    }

    /**
     * Get a Value from the DOMXPath
     * @param  object $oXpath    DOMXPath object
     * @param  object $oNode     the child node (use null if no child)
     * @param  string $sSelector the XPATH selector
     * @return string            returns a clean string
     */
    private function getValue($oXpath, $oNode, $aSelector, $bForceString = true) {
        if (empty($aSelector['selector'])) {
            return '';
        }

        $sSelector = $aSelector['selector'];
        $sAttribute = !empty($aSelector['attribute']) ? $aSelector['attribute'] : false;

        try {
            $oResult = $oXpath->query($sSelector, $oNode);

        } catch (Exception $e) {
            return '';
        }

        if (!$bForceString) {
            return $oResult;
        }

        if (empty($oResult->length) || $oResult->length === 0) {
            return '';
        }

        if ($sAttribute) {
            $sValue = $oResult[0]->getAttribute($sAttribute);
        } else {
            $sValue = $oResult[0]->nodeValue;
        }

        return trim(strip_tags($sValue));
    }

    /**
     * Force to clear all the cached files
     * @return int number of files deleted
     */
    public function forceClearCache() {
        return $this->clearCache(true);
    }

    /**
     * Force to clear the cache
     * @param  boolean $bForce [description]
     * @return [type]          [description]
     */
    private function clearCache($bForce = false) {
        if ($this->iRefreshRate == 0) $bForce = true;
        if ($this->sPathCache && @file_exists($this->sPathCache) || $bForce) {
            $aFiles = glob($this->sPathCache . '*.*');
            if (is_array($aFiles) && count($aFiles)>0) {
                foreach ($aFiles as $sFilename) {
                    if (@file_exists($sFilename) && $iFileCreationTime = @filemtime($sFilename)) {
                        $iFileAge = time() - $iFileCreationTime;
                        if ($iFileAge > ($this->iRefreshRate * 60) || $bForce) @unlink($sFilename);
                    }
                }
            }
            return count($aFiles);
        }
        return false;
    }

    /**
     * Loads the cached data
     * @param  string $sFilename the cached filename
     * @return array             returns an array on success
     */
    private function loadCache($sName) {
        if (!$this->sPathCache) {
            return false;
        }

        $sFilename = $this->sPathCache . $sName . '.tmp';

        if (file_exists($sFilename)) {
            return json_decode(file_get_contents($sFilename), true);
        } else {
            return false;
        }
    }

    /**
     * Saves the data into the cache
     * @param  string  $sFilename the name of the file
     * @param  array   $sData     the data
     * @return boolean            returns true when saved
     */
    private function saveCache($sName, $aData) {
        if (!$this->sPathCache) {
            return false;
        }

        $sFilename = $this->sPathCache . $sName . '.tmp';

        $handle = fopen($sFilename, 'w');

        if($handle !== FALSE) {
            fwrite ($handle, json_encode($aData));
            fclose ($handle);
            return true;
        }
        return false;
    }

    /**
     * Check if value exists in subarray
     * @param  array    $aArray The array to search through
     * @param  string   $sKey   The key of the subarray
     * @param  string   $sValue The value to search for
     * @return boolean          returns true if found
     */
    private function inSubArray($aArray, $sKey, $sValue) {
        foreach ($aArray as $aValue) {
            if ($aValue[$sKey] === $sValue) {
                return true;
            }
        }
        return false;
    }
}
