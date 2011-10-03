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
     * StreamManager class
     *
     * @category  PHP
     * @package   TheSeer\fStream
     * @author    Arne Blankerts <arne@blankerts.de>
     * @access    public
     *
     */
    class StreamManager {

        protected $streams = array();

        public function register($proto, $class = 'TheSeer\\Tools\\SimpleStream') {
            if (!is_subclass_of($class, 'TheSeer\\Tools\\AbstractStream')) {
                throw new StreamManagerException("'$class' does not inherit from AbstractStream.", StreamManagerException::WrongInheritence);
            }

            if (in_array($proto, stream_get_wrappers())) {
                throw new StreamManagerException("'$proto' is already registered.", StreamManagerException::AlreadyRegistered);
            }
            $config = new StreamProperties($proto);
            $this->streams[$proto] = $config;
            $class::setProperties($config);
            stream_wrapper_register($proto,  $class);
            return $config;
        }

        public function unregister($proto) {
            stream_wrapper_unregister($proto);
            unset($this->streams[$proto]);
        }

        public function getStreamProperties($proto) {
            if (!isset($this->stream[$proto])) {
                throw new StreamManagerException("Protocol '$proto' not registered with StreamManager", StreamManagerException::NotRegistered);
            }
            return $this->streams[$proto];
        }

    }

    /**
     * StreamManagerException class
     *
     * @access   public
     * @author   Arne Blankerts <arne@blankerts.de>
     *
     */
    class StreamManagerException extends \Exception {

        const AlreadyRegistered = 1;
        const NotRegistered = 2;
        const WrongInheritence = 3;

    }

}