<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Ranyuen\Template\Template;
use Ranyuen\Model\Photo;
use Ranyuen\Model\Util;

/**
 * Admin photos.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/admin')
 */
class AdminPhotosController extends AdminController
{
    private $galleryPath = 'images/gallery/';
    private $assetsPath  = 'images/assets_new/';

    /**
     * AdminPhotos index
     *
     * @param Router  $router Router.
     *
     * @Route('/photos/')
     */
    public function photos(Router $router)
    {
        $this->auth();
        $insertData = Photo::getInsertedPhotos();

        return $this->renderer->render(
            'admin/photos/index',
            [
                'photos_createdAt' => $insertData,
            ]
        );
    }

    /**
     * [photosAssets description]
     * @param  Router $router [description]
     * @return [type]         [description]
     *
     * @Route('/photos/assets')
     */
    public function photosAssets(Router $router)
    {
        $this->auth();

        return $this->renderer->render(
            'admin/photos/assets', []
        );
    }

    /**
     * registrePhotos
     *
     * @Route('/photos/', via=POST)
     */
    public function registerPhotos()
    {
        if (!Util::uploadFileValidator($_FILES['folder_select'])) return;
        if (!isset($_POST['photos_upload']) || empty($_POST['description_en']) || empty($_POST['description_ja']) || empty($_POST['species_name'])) return;

        for ($i = 0; $i < count($_FILES['folder_select']['name']); $i++) {
            $fileExt = pathinfo($_FILES['folder_select']['name'][$i], PATHINFO_EXTENSION);
            if (Util::fileExtensionGetAllowUpload($fileExt) && is_uploaded_file($_FILES['folder_select']['tmp_name'][$i])) {

                $photoSize = getimagesize($_FILES['folder_select']['tmp_name'][$i]);
                $uuid = Util::makeUuid();
                move_uploaded_file($_FILES['folder_select']['tmp_name'][$i], $this->galleryPath . $uuid . '.' . $fileExt);

                $photo = new Photo();
                $photo->uuid           = $uuid;
                $photo->description_ja = htmlspecialchars($_POST['description_ja']);
                $photo->description_en = htmlspecialchars($_POST['description_en']);
                $photo->color          = htmlspecialchars($_POST['color']);
                $photo->product_name   = htmlspecialchars($_POST['product_name']);
                $photo->species_name   = htmlspecialchars($_POST['species_name']);
                $photo->width          = $photoSize[0];
                $photo->height         = $photoSize[1];
                $photo->save();
            }
        }

        return new Response('', 303, ['Location' => '/admin/photos/']);
    }

    /**
     * registerPhotosAssets
     *
     * @param  Router $router [description]
     * @return [type]         [description]
     *
     * @Route('/photos/assets', via=POST)
     */
    public function registerPhotosAssets()
    {
        $uploaded = array();

        if (!isset($_POST['photos_assets_upload'])) return;
        if (!Util::uploadFileValidator($_FILES['folder_select'])) {
            return new Response('', 303, ['Location' => '/admin/photos/assets']);
        }

        for ($i = 0; $i < count($_FILES['folder_select']['name']); $i++) {
            $fileExt = pathinfo($_FILES['folder_select']['name'][$i], PATHINFO_EXTENSION);
            if (Util::fileExtensionGetAllowUpload($fileExt) && is_uploaded_file($_FILES['folder_select']['tmp_name'][$i])) {
                $uuid = Util::makeUuid();
                move_uploaded_file($_FILES['folder_select']['tmp_name'][$i], $this->assetsPath . $uuid . '.' . $fileExt);
                array_push($uploaded, $uuid);
            }
        }
        return $this->renderer->render(
            'admin/photos/confirm_upload',
            [
                'uploaded' => $uploaded,
            ]
        );
    }

    /**
     * deletePhotos
     *
     * @return [type] [description]
     *
     * @Route('/photos/delete', via=POST)
     */
    public function deletePhotos()
    {
        if (!isset($_POST['delete_photos'])) return;
        $photos = Photo::where('created_at', $_POST['datetime'])->get();
        foreach ($photos as $photo) {
            unlink($this->galleryPath . $photo->uuid . '.jpg');
        }
        Photo::where('created_at', $_POST['datetime'])->delete();

        return new Response('', 303, ['Location' => '/admin/photos/']);
    }
}
