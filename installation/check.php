<?php


									$writableDirs = array(
										ROOT . '/app/config/',
										ROOT . '/app/config/bootstrap.php',
										ROOT . '/app/tmp',
										ROOT . '/app/tmp' . DS . 'sessions',
										ROOT . '/app/tmp' . DS . 'logs',
										ROOT . '/app/tmp' . DS . 'cache',
										ROOT . '/app/tmp' . DS . 'tests',
										ROOT . '/app/webroot' . DS . 'upload',
										ROOT . '/app/webroot' . DS . 'img' . DS . 'logo',
									);
									$areNotWriteable = array();
									$areWritable = array();
									foreach ($writableDirs as $dir) {
										if (!is_dir($dir)) {
											mkdir($dir);
										}
										if (!is_writable($dir)) {
											$areNotWriteable[] = $dir;
										} else {
											$areWritable[] = $dir;
										}
										unset($dir);
									}
									$this->set('areNotWriteable', $areNotWriteable);
									$this->set('areWritable', $areWritable);
									if (count($areNotWriteable)) {
										$this->set(compact('areNotWriteable'));
										unset($areNotWriteable);
									}

								
								?> 
