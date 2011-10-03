<?php
/**
 * Copyright (c) 2011 Arne Blankerts <arne@blankerts.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Arne Blankerts nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   TheSeer\fStream
 * @author    Arne Blankerts <arne@blankerts.de>
 * @copyright Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/theseer/fStream
 *
 */

namespace TheSeer\Tools {

    /**
     * fStreamUri data class
     *
     * This class will handle two different formats of URIs:
     *
     *    proto:///path/to/file.ext  (full path)
     *    proto://path/to/file.ext   (relative path)
     *
     * The relative format is considered language dependend while the full path version
     * does *not* get the language included. Resolving the actual language is out of scope for this class
     *
     * @category Core
     * @package  Core
     * @access   public
     * @author   Arne Blankerts <arne@blankerts.de>
     *
     */
    class StreamUri extends AbstractProperties {

        public function __construct($uri) {

            /*
             *  Since parse_url refuses to handle our 1st uri format (http://bugs.php.net/bug.php?id=28931)
             *  we use a regex to split up the path (regex from rfc #2396). Regex result array:
             *
             *    $0 = <full url>
             *    $1 = http:
             *    $2 = http
             *    $3 = //www.ics.uci.edu
             *    $4 = www.ics.uci.edu
             *    $5 = /pub/ietf/uri/
             *    $6 = ?para=value
             *    $7 = para=value
             *    $8 = #Related
             *    $9 = Related
             *
             *  There is no need to support username / passwords and ports in our context
             */
            $tmp=array();
            preg_match("=^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?=",$uri,$tmp);

            // build path from hostname and path
            if ($tmp[4]!='') {
                $path = '/'.$tmp[4].$tmp[5];
            } else {
                $path = $tmp[5];
            }

            // sanatise path
            $sanatised=array();
            foreach (explode('/',$path) as $part) {
                if ($part=='') continue;
                if ($part=='..') array_pop($sanatised);
                else $sanatised[]=$part;
            }
            $path = join($sanatised,'/');

            $this->data = array(
                'uri'         => $uri,
                'protocol'    => $tmp[2],
                'path'        => $path
            );

            // parse query part
            if (isset($tmp[7])) {
                parse_str($tmp[7], $this->data['query']);
            }
            if (isset($tmp[9])) {
                $this->data['fragment'] = $tmp[9];
            }

        }

    }

}