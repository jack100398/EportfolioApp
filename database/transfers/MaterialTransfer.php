<?php

namespace Database\Transfers;

use App\Models\Course\Course;
use App\Models\File;
use App\Models\Material\CourseMaterial;
use App\Models\Material\Material;
use App\Models\Material\MaterialDownloadHistory;
use Illuminate\Support\Facades\DB;

class MaterialTransfer
{
    private const FILE = 0;

    private const URL = 1;

    private const FOLDER = 2;

    public function index()
    {
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->TransferMaterialFolder();
            $this->TransferMaterialFile();
            $this->TransferMaterialAuthorize();
            $this->TransferCourseMaterial();
            $this->TransferDownloadHistory();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    public function TransferMaterialFile()
    {
        DB::connection('raw')
        ->table('common_filemanager_file_info')
        ->whereNotIn('file_type', ['folder'])
        ->get()
        ->each(function ($file) {
            echo "file $file->file_id begin \n";
            File::forceCreate([
                'id' => $file->file_id,
                'name' => $file->file_name,
                'extension' => $file->file_type,
                'size' => $file->file_size,
                'directory' => "file_path/ $file->file_id", //TODO:CHECK FILE PATH
                'remarks' => '',
                'created_by' => $file->upload_user_id,
                'created_at' => $file->upload_date,
            ]);

            Material::forceCreate([
                'id' => $file->file_id,
                'folder_id' => $file->parent_folder === 0 ? null : $file->parent_folder,
                'type' => self::FILE,
                'source' => $file->file_id,
                'owner' => $file->upload_user_id,
                'deleted_at' => $file->is_delete === 1 ? $file->upload_date : null,
            ]);
            echo "file $file->file_id Finished \n";
        });
    }

    public function TransferMaterialFolder()
    {
        DB::connection('raw')
        ->table('common_filemanager_file_info')
        ->where('file_type', 'folder')
        ->get()
        ->each(function ($folder) {
            echo "folder $folder->file_id Begin \n";
            Material::forceCreate([
                'id' => $folder->file_id,
                'folder_id' => $folder->parent_folder === 0 ? null : $folder->parent_folder,
                'type' => self::FOLDER,
                'source' => $folder->file_name,
                'owner' => $folder->upload_user_id,
                'deleted_at' => $folder->is_delete === 1 ? $folder->upload_date : null,
            ]);
            echo "folder $folder->file_id Finished \n";
        });
    }

    public function TransferMaterialAuthorize()
    {
        DB::connection('raw')
        ->table('common_filemanager_authorize')
        ->where('is_delete', 0)
        ->get()
        ->each(function ($authorize) {
            echo "authorize $authorize->authorize_id Begin \n";
            if ($authorize->authorize_user_id != null) {
                Material::findOrFail($authorize->file_id)->authUser()->attach($authorize->authorize_user_id);
            } else {
                Material::findOrFail($authorize->file_id)->authUser()->attach($authorize->authorize_group_id); //TODO:GROUP CLASS
            }
            echo "authorize $authorize->authorize_id Finished \n";
        });
    }

    public function TransferCourseMaterial()
    {
        DB::connection('raw')
        ->table('common_course_material')
        ->where('is_delete', 0)
        ->get()
        ->each(function ($courseMaterial) {
            if (Course::find($courseMaterial->course_id) === null) {
                echo "CourseId $courseMaterial->course_id NOT FOUND\n";

                // return true; TODO:OPEN IT WHEN COURSE IS ALL SUCCESSFULLY TRANSFER
            }

            $materialId = $courseMaterial->filemanager_file_id;
            if ($materialId === null) {
                if (filter_var($courseMaterial->extension, FILTER_VALIDATE_URL)) {
                    $materialId = $this->CreateURLMaterial($courseMaterial->extension);
                } else {
                    $materialId = File::forceCreate([
                        'name' => $courseMaterial->description,
                        'extension' => $courseMaterial->extension,
                        'size' => 0,
                        'directory' => "material_file_path/ $courseMaterial->material_id", //TODO:CHECK FILE PATH
                        'remarks' => '',
                        'created_by' => $courseMaterial->user_id,
                        'created_at' => $courseMaterial->mtime,
                    ])->id;
                }
            }

            CourseMaterial::forceCreate([
                'id' => $courseMaterial->material_id,
                'course_id' => $courseMaterial->course_id,
                'material_id' => $materialId,
                'description' => $courseMaterial->description,
                'required_time' => gmdate('H:i:s', $courseMaterial->min_read_time),
                'opened_at' => $courseMaterial->open_start_time,
                'ended_at' => $courseMaterial->open_end_time,
                'created_by' => $courseMaterial->user_id,
                'updated_by' => $courseMaterial->user_id,
                'created_at' => $courseMaterial->mtime,
                'updated_at' => $courseMaterial->mtime,
            ]);
            echo "Material $courseMaterial->material_id Fin\n";
        });
    }

    public function TransferDownloadHistory()
    {
        $index = 0;
        DB::connection('raw')
        ->table('common_course_material_download_list')
        ->where('is_delete', 0)
        ->orderBy('id')
        ->chunk(100, function ($list) use (&$index) {
            $list->each(function ($history) {
                if (CourseMaterial::find($history->material_id) === null) {
                    echo "Material $history->material_id NOT FOUND\n";

                    return true;
                }
                MaterialDownloadHistory::forceCreate([
                    'id' => $history->id,
                    'course_material_id' => $history->material_id,
                    'student' => $history->user_id,
                    'opened_counts' => $history->times,
                    'downloaded_counts' => $history->download_times,
                    'reading_time' => $history->read_time !== null ? $history->read_time : gmdate('H:i:s', 0),
                    'created_at' => $history->first_time,
                    'updated_at' => $history->mtime,
                ]);
            });

            echo $index.' to '.($index + 100).'Finished'."\n";

            $index += 100;
        });
    }

    private function CreateURLMaterial($source): int
    {
        return Material::create(['type' => self::URL, 'source' => $source])->id;
    }
}
