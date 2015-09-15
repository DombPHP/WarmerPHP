<?php
/**
 * Warmer
 *
 * An open source web application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Michael Lee
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @copyright    2015 Michael Lee
 * @author       Micheal Lee <michaellee15@sina.com>
 * @license      The MIT License (MIT)
 * @version      0.2.0
 */

namespace Micsqli;

/**
 * 多数据库连接类
 */
class MultiMysqli extends Mysqli {
	/**
	 * 数据库连接资源对象数组
	 *
	 * @access private
	 * @var string
	 */
	private $links = array();
	
	/**
	 * 静态类实例
	 *
	 * @access private
	 * @var MultiMysqli
	 */
	private static $instance;
	
	public function __construct($conf) {
		parent::__construct($conf);
	}
	
	/**
	 * 获取类实例
	 *
	 * @access public
	 * @param array $conf 配置参数
	 * @return MultiMysqli
	 */
	public static function getInstance(&$conf) {
		if(self::$instance && self::$instance instanceof self) {
			return self::$instance;
		} else {
			self::$instance = new self($conf);
			return self::$instance;
		}
	}
	
	/**
	 * 多数据库连接方法
	 *
	 * @access public
	 * @param array 配置参数
	 * @return void
	 */
	public function multiConnect($conf) {
		$no = md5(serialize($conf));
		if(isset($this->links[$no])) {
			$this->link = $this->links[$no];
			return $this->links[$no];
		} else {
			$this->link = $this->connect($conf);
			$this->links[$no] = $this->link;
		}
	}
	
	/**
	 * 执行查询语句
	 *
	 * @access private
	 * @param string $sql 查询语句
	 * @return mixed
	 */
	protected function _query($sql) {
		$this->multiConnect($this->conf);
		$result = $this->link->query($sql);
		if($this->link->errno) {
			trigger_error($this->link->error.'; SQL:'.$sql);
		}
		return $result;
	}
	
	/**
	 * 设置配置参数
	 *
	 * @access public
	 * @param array $conf 配置参数
	 * @return void
	 */
	public function setConf($conf) {
		$this->conf = $conf;
	}
}