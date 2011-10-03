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
     * SimpleStream class is a simple wrapper to map "protocols" into filesystem relative to a configured base directory
     *
     * @category  PHP
     * @package   TheSeer\fStream
     * @author    Arne Blankerts <arne@blankerts.de>
     * @access    public
     *
     */
    class SimpleStream extends AbstractStream {

        /**
         * Filepointer
         *
         * var filepointer
         */
        protected $fp;

        /**
         * Directory handle
         *
         * var dirhandle
         */
        protected $dh;

        /**
         * Constructor
         *
         * return void
         */
        public function __construct() {
            $this->fp=0;
            $this->dh=0;
        }

        protected function translate($uri) {
            $res = new StreamUri($uri);
            return self::$properties[$res->protocol]->baseDir . '/' . $res->path;
        }

        public function stream_open($path, $mode, $options, &$opened_path) {
            $this->fp = fopen($this->translate($path), $mode, $options);
            return $this->fp !== false;
        }

        public function stream_close() {
            fclose($this->fp);
            $this->fp = 0;
            return;
        }

        public function stream_read($count) {
            return fread($this->fp, $count);
        }

        public function stream_write($data) {
            return fwrite($this->fp, $data);
        }

        public function stream_eof() {
            return feof($this->fp);
        }

        public function stream_tell() {
            return ftell($this->fp);
        }

        public function stream_seek($offset,$whence ){
            return fseek($this->fp, $offset, $whence);
        }

        public function stream_flush() {
            return fflush($this->fp);
        }

        public function stream_stat() {
            return fstat($this->fp);
        }

        public function unlink($path) {
            return unlink($this->translate($path));
        }

        public function rename($from,$to) {
            return rename($this->translate($from), $this->translate($to));
        }

        public function url_stat($path, $flags) {
            $fname=$this->translate($path);
            if (!file_exists($fname)) {
                return false;
            }
            return stat($fname);
        }

        public function mkdir($path, $mode, $options) {
            return mkdir($this->translate($path), $mode, $options);
        }

        public function rmdir($path, $options) {
            return rmdir($this->translate($path));
        }

        public function dir_opendir($path, $options) {
            $this->dh=opendir($this->translate($path));
            return $this->dh > 0;
        }

        public function dir_readdir() {
            return readdir($this->dh);
        }

        public function dir_rewinddir() {
            return rewinddir($this->dh);
        }

        public function dir_closedir() {
            closedir($this->dh);
            $this->dh=0;
            return true;
        }
    }
}