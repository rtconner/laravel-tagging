<?php

namespace Conner\Tagging\Console\Commands;

use Conner\Tagging\TaggingUtility;
use Conner\Tagging\Model\TagGroup;
use Illuminate\Console\Command;

class GenerateTagGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagging:create-group {group_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a laravel tag group';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groupName = $this->argument('group_name');

        $tagGroup = new TagGroup();
        $tagGroup->name = $groupName;
        $tagGroup->slug = TaggingUtility::normalize($groupName);

        $tagGroup->save();

        $this->info('Created tag group: ' . $groupName);
    }
}
