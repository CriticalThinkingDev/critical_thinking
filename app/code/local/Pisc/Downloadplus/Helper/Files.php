<?php
/**
 * Based in FILE_UTIL from PEAR Repository.
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Files helper
 *
 * @category   Pisc
 * @package    Mage_Downloadplus
 * @author
 * @version		0.1.1
 */

class Pisc_Downloadplus_Helper_Files extends Mage_Core_Helper_Abstract
{

	/**
	 * Sorting Constants
	 */
	const FILE_SORT_NONE = 0;
	const FILE_SORT_REVERSE = 0;
	const FILE_SORT_NAME = 2;
	const FILE_SORT_SIZE = 4;
	const FILE_SORT_DATE = 8;
	const FILE_SORT_RANDOM = 16;

	/**
	 * Listing Constants
	 */
	const FILE_LIST_FILES = 1;
	const FILE_LIST_DIRS = 2;
	const FILE_LIST_DOTS = 4;
//	const FILE_LIST_ALL = $this->FILE_LIST_FILES | $this->FILE_LIST_DIRS | $this->FILE_LIST_DOTS;

	/**
	 * List Directory
	 *
	 * The final argument, $cb, is a callback that either evaluates to true or
	 * false and performs a filter operation, or it can also modify the
	 * directory/file names returned.  To achieve the latter effect use as
	 * follows:
	 *
	 * <code>
	 * <?php
	 * function uc(&$filename) {
	 *     $filename = strtoupper($filename);
	 *     return true;
	 * }
	 * $entries = File_Util::listDir('.', FILE_LIST_ALL, FILE_SORT_NONE, 'uc');
	 * foreach ($entries as $e) {
	 *     echo $e->name, "\n";
	 * }
	 * ?>
	 * </code>
	 *
	 * @static
	 * @access  public
	 * @return  array
	 * @param   string  $path
	 * @param   int     $list
	 * @param   int     $sort
	 * @param   mixed   $cb
	 */
	public function getDir($path, $list = FILE_LIST_ALL, $sort = FILE_SORT_NONE, $cb = null)
	{
		if (!strlen($path) || !is_dir($path)) {
			return null;
		}

		$entries = array();
		for ($dir = dir($path); false !== $entry = $dir->read(); ) {
			if ($list & FILE_LIST_DOTS || $entry{0} !== '.') {
				$isRef = ($entry === '.' || $entry === '..');
				$isDir = $isRef || is_dir($path .DS. $entry);
				if (    ((!$isDir && $list & FILE_LIST_FILES)   ||
				($isDir  && $list & FILE_LIST_DIRS))   &&
				(!is_callable($cb) ||
				call_user_func_array($cb, array(&$entry)))) {
					$entries[] = (object) array(
                        'name'  => $entry,
                        'size'  => $isDir ? null : filesize($path .DS. $entry),
                        'date'  => filemtime($path .DS. $entry),
					);
				}
			}
		}
		$dir->close();

		if ($sort) {
			$entries = $this->sortFiles($entries, $sort);
		}

		return $entries;
	}

	/*
	 * Function to recursively return all files within a directory.
	 * http://www.pgregg.com/projects/php/code/recursive_readdir.phps
	 * Author: Paul Gregg
	 * http://www.pgregg.com
	 *
	 * For a more robust and featureful recursive directory listing tool
	 * have a look at preg_find:
	 * http://www.pgregg.com/projects/php/preg_find/preg_find.phps
	 * Example uses: http://www.pgregg.com/forums/viewtopic.php?tid=73
	 */
	public function getDirRecursive($start_dir='.')
	{

		$files = array();
		if (is_dir($start_dir)) {
			$fh = opendir($start_dir);
			while (($file = readdir($fh)) !== false) {
				# loop through the files, skipping . and .., and recursing if necessary
				if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
				$filepath = $start_dir .DS. $file;
				if ( is_dir($filepath) )
					$files = array_merge($files, $this->getDirRecursive($filepath));
				else
					array_push($files, $filepath);
			}
			closedir($fh);
		} else {
			# false if the function was called with an invalid non-directory argument
			$files = false;
		}

		return $files;
	}

	/**
	 * Sort Files
	 *
	 * @static
	 * @access  public
	 * @return  array
	 * @param   array   $files
	 * @param   int     $sort
	 */
	public function sortFiles($files, $sort)
	{
		if (!$files) {
			return array();
		}

		if (!$sort) {
			return $files;
		}

		if ($sort === 1) {
			return array_reverse($files);
		}

		if ($sort & FILE_SORT_RANDOM) {
			shuffle($files);
			return $files;
		}

		$names = array();
		$sizes = array();
		$dates = array();

		if ($sort & FILE_SORT_NAME) {
			$r = &$names;
		} elseif ($sort & FILE_SORT_DATE) {
			$r = &$dates;
		} elseif ($sort & FILE_SORT_SIZE) {
			$r = &$sizes;
		} else {
			asort($files, SORT_REGULAR);
			return $files;
		}

		$sortFlags = array(
			FILE_SORT_NAME => SORT_STRING,
			FILE_SORT_DATE => SORT_NUMERIC,
			FILE_SORT_SIZE => SORT_NUMERIC,
		);

		foreach ($files as $file) {
			$names[] = $file->name;
			$sizes[] = $file->size;
			$dates[] = $file->date;
		}

		if ($sort & FILE_SORT_REVERSE) {
			arsort($r, $sortFlags[$sort & ~1]);
		} else {
			asort($r, $sortFlags[$sort]);
		}

		$result = array();
		foreach ($r as $i => $f) {
			$result[] = $files[$i];
		}

		return $result;
	}

	/*
	 * Replaces all Backslashes to forward Slashes
	 */
	public function makePath($path)
	{
		$result = str_replace(DS, "/", $path);
		return $result;
	}

	/*
	 * Replaces all forwards Slashes to Directory Separator
	 */
	public function makeFile($path)
	{
		$result = str_replace("/", DS, $path);
		return $result;
	}

	/*
	 * Returns boolean if file path is part of path
	 */
	public function isInPath($file, $path)
	{
		$result = (substr($file, 0, strlen($path)))==$path;

		return $result;
	}

	/*
	 * Removes a path from a file path
	 */
	public function removePath($file, $path)
	{
		$pattern = '~^'.addslashes($path).'~';
		$result = preg_replace($pattern, '', $file);

		return $result;
	}

}
