<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Employee;
use App\Models\KindergartenGroup;
use App\Models\News;
use App\Services\FileService;
use DB;
use Exception;
use Illuminate\Console\Command;

/**
 * Class ClearDataCommand
 * @package App\Console\Commands
 */
class ClearDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear tables and files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $fileService = new FileService();

        // Delete albums with photos
        $albums = Album::get();
        foreach ($albums as $album) {
            foreach ($album->photos as $photo) {
                $fileService->delete($photo->file);
                $photo->delete();
            }
            $album->delete();
        }

        // Delete employees
        $employees = Employee::get();
        foreach ($employees as $employee) {
            $employeePhotoFile = $employee->file;
            if ($employeePhotoFile)
                $fileService->delete($employeePhotoFile);

            $employee->delete();
        }

        // Delete kindergarten groups
        $kindergartenGroups = KindergartenGroup::get();
        foreach($kindergartenGroups as $kg) {
            foreach ($kg->files as $file) {
                $fileService->delete($file);
            }

            DB::table('kindergarten_groups_files')
                ->where('kindergarten_group_id', '=', $kg->id)
                ->delete();

            $kg->delete();
        }

        // Delete news
        $news = News::get();
        foreach ($news as $n) {
            if ($n->file)
                $fileService->delete($n->file);
        }

        //TRUNCATE
        DB::table('albums')->truncate();
        DB::table('photos')->truncate();
        DB::table('files')->truncate();
        DB::table('employees')->truncate();
        DB::table('kindergarten_groups')->truncate();
        DB::table('kindergarten_groups_files')->truncate();

        DB::table('documents')->truncate();
        DB::table('document_groups')->truncate();
        DB::table('news')->truncate();
        DB::table('tt_groups')->truncate();
        DB::table('tts')->truncate();

        DB::table('oauth_access_tokens')->truncate();


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return 0;
    }
}
