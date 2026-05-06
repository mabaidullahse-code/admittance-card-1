<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Jobs\GenerateAdmitCardJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PreGenerateAdmitCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admit-cards:generate {--force : Regenerate all even if they exist} {--limit= : Limit the number of students to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk generate admit card PDFs in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $limit = $this->option('limit');

        $query = Student::query();

        if ($limit) {
            $query->limit($limit);
        }

        $students = $query->get();
        $count = $students->count();

        $this->info("Dispatching generation jobs for {$count} students...");
        $bar = $this->output->createProgressBar($count);

        $bar->start();

        foreach ($students as $student) {
            $fileName = "admit_cards/Admit_Card_{$student->student_id}.pdf";
            $exists = Storage::disk('public')->exists($fileName);

            if (!$exists || $force) {
                GenerateAdmitCardJob::dispatch($student->id, 'artisan_command');
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All jobs have been dispatched to the queue.');
    }
}
