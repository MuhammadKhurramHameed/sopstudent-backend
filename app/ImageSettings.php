<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Image;
class ImageSettings extends Model
{
    ////////////////////////////
    // Profile Upload Options //
    ////////////////////////////
    protected $prefix = 'public/';
    protected $profilePicsPath      = "uploads/users/";
    protected $profilePicsThumbnailpath = "uploads/users/thumbnail/";
    protected $thumbnailSize = 50;
    protected $profilePicSize = 140;
    protected $defaultProfilePicPath           = "uploads/users/default.png";
    protected $defaultprofilePicsThumbnailpath = "uploads/users/thumbnail/default.png";
    protected $settingsImagePath = "uploads/settings/";

    protected $resumePicsPath      = "uploads/resumes/";
    protected $defaultResumeTemplateImgpath = "uploads/resumes/default_resume.png";


    ///////////////////////////////////
    // Image Question upload options //
    ///////////////////////////////////
    protected $examImagepath                = "uploads/exams/";
    protected $examImageSize                = 600;
    protected $examMaxFileSize              = 10000;

    protected $blogImgPath             = "uploads/blogs/";
    protected $blogImgThumbnailpath    = "uploads/blogs/thumbnail/";
    protected $blogThumbnailSize    = 200;
    protected $blogImgSize          = 1200;
    protected $defaultBlogImgPath               = "uploads/blogs/default.jpg";
    protected $defaultBlogImgThumbnailpath      = "uploads/blogs/thumbnail/default.jpg";

    function __construct() {
        $server_software = ! empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        if ( ! empty( $server_software ) ) {
            if(strpos($server_software, 'nginx') !== false){
                $this->prefix = '';
            }
        }

     }
     /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getResumePicsPath()
    {
        return $this->prefix . $this->resumePicsPath;
    }

     /**
     * Returns the Deafult Resume Template Path
     * @return [string] [description]
     */
    public function getDefaultResumeTemplateImgpath()
    {
        return $this->prefix . $this->defaultResumeTemplateImgpath;
    }



    /**
     * If Needed can change the Profile Pics Path
     * @param [string] $path [description]
     * @return  void
     */
    public function setProfilePicsPath($path)
    {
        $this->profilePicsPath = $path;
    }

    /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getDefaultProfilePicPath()
    {
        return $this->prefix . $this->defaultProfilePicPath;
    }

      /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getDefaultprofilePicsThumbnailpath()
    {
        return $this->prefix . $this->defaultprofilePicsThumbnailpath;
    }


    /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getProfilePicsPath()
    {
        return $this->prefix . $this->profilePicsPath;
    }

    /**
     * Returns the Profile Thumbnail Path
     * @return [string] [description]
     */
    public function getProfilePicsThumbnailpath()
    {
        return $this->prefix . $this->profilePicsThumbnailpath;
    }

    /**
     * Returns the Thumbnail size
     * @return [numeric] [description]
     */
    public function getThumbnailSize()
    {
        return $this->thumbnailSize;
    }

    /**
     * Returns the Profile Pic size
     * @return [numeric] [description]
     */
    public function getProfilePicSize()
    {
        return $this->profilePicSize;
    }

    /**
     * If needed can change the Thumb size
     * @param [Integer] $size [description]
     * @return  void [<description>]
     */
    public function setThumbnailSize($size)
    {
        $this->thumbnailSize = $size;
    }


    public function getExamImagePath()
    {
        return $this->prefix . $this->examImagepath;
    }

    public function getExamImageSize()
    {
        return $this->examImageSize;
    }

    public function getExamMaxFilesize()
    {
        return $this->examMaxFileSize;
    }

    public function getSettingsImagePath()
    {
        return $this->prefix . $this->settingsImagePath;
    }
    /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getDefaultBlogImgPath()
    {
        return $this->prefix . $this->defaultBlogImgPath;
    }

      /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getDefaultBlogImgThumbpath()
    {
        return $this->prefix . $this->defaultBlogImgThumbnailpath;
    }


     /**
     * Returns the Profile Pics Path
     * @return [string] [description]
     */
    public function getBlogImgPath()
    {
        return $this->prefix . $this->blogImgPath;
    }

    /**
     * Returns the Profile Thumbnail Path
     * @return [string] [description]
     */
    public function getBlogImgThumbnailpath()
    {
        return $this->prefix . $this->blogImgThumbnailpath;
    }

    /**
     * Returns the Thumbnail size
     * @return [numeric] [description]
     */
    public function getBlogThumbnailSize()
    {
        return $this->blogThumbnailSize;
    }

    /**
     * Returns the Profile Pic size
     * @return [numeric] [description]
     */
    public function getBlogImgSize()
    {
        return $this->blogImgSize;
    }

}
