<?php

namespace App\Console\Commands;

use App\Mail\NotifEndDateCertificateMail;
use Carbon\Carbon;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckEndDateCertificateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate:enddate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check end date certificates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataEndDate = Certificate::where('end_date', '<=', Carbon::now()->addDay(7))
            ->where('isNotif', 1)->get();

        $notifiedUsers = [];

        if ($dataEndDate->count() >= 1) {
            foreach ($dataEndDate as $endDate) {
                $user = User::where('id', $endDate->user_created)->first();

                if (!in_array($user->id, $notifiedUsers)) {
                    Mail::to($user->email)->send(new NotifEndDateCertificateMail($dataEndDate, $user));
                    $notifiedUsers[] = $user->id;
                }
            }
        }
    }
}
