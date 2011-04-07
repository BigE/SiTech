<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace SiTech\Routing;

/**
 * @see SiTech\Exception
 */
require_once('SiTech/Exception.php');

/**
 * Routing exception class.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Routing
 * @version $Id$
 */
class Exception extends \SiTech\Exception {}

/**
 * This exception is thrown when no route is matched through the router.
 * 
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Routing
 * @version $Id$
 */
class NoRouteException extends Exception {}

/**
 * This exception is thrown when an action is not found in a controller. It can
 * successfully be caught to produce a 404 error if you so desire.
 * 
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Routing
 * @version $Id$
 */
class MethodNotFoundException extends Exception {}