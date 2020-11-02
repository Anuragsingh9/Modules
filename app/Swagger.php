<?php

namespace App;
/**
 * @license Apache 2.0
 */

use Illuminate\Database\Eloquent\Model;

/**
 * Class Swagger
 *
 * @package App\Model
 *
 * @author  Anurag singh <anurag@pebibits.com>
 * /**
 * @SWG\Definition(
 *     * @SWG\Schema(
 *     description="swagger model",
 *     title="swagger model",
 *     required={"name", "email","address"},
 *     )
 * )
 */
class Swagger extends Model
{

     /**
     * @SWG\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     * @var integer
     */
    private $id;

    /**
     * @SWG\Property(
     *     format="string",
     *     description="name",
     *     title="name",
     * )
     *
     * @var String
     */
    private $email;

    /**
     * @SWG\Property(
     *     format="string",
     *     description="email",
     *     title="email",
     * )
     *
     * @var String
     */
    private $name;

    /**
     * @SWG\Property(
     *     format="string",
     *     description="address",
     *     title="address",
     * )
     * @var String
     */
    private $address;
    protected $table = 'swaggers';
    public $timestamps = false;
    protected $fillable = ['name','email','address'];
}
