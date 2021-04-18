<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\SyncFiles;
use MusicSync\Test\DirectoryTestHarness as TestDirectory;
use MusicSync\Test\TestCase;

class SyncFilesTest extends TestCase
{
    protected TestFileOperationFactory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new TestFileOperationFactory();
    }

    public function testSimpleSyncNullCase()
    {
        $operations = $this->runSync($this->createStructure1(), $this->createStructure1());
        $expected = [
            ['type' => 'noop', 'details' => 'a and a identical'],
            ['type' => 'noop', 'details' => 'b and b identical'],
            ['type' => 'noop', 'details' => 'd and d identical'],
            ['type' => 'noop', 'details' => 'd-a and d-a identical'],
            ['type' => 'noop', 'details' => 'd-c and d-c identical'],
            ['type' => 'noop', 'details' => 'e and e identical'],
            ['type' => 'noop', 'details' => 'e-a and e-a identical'],
            ['type' => 'noop', 'details' => 'e-b and e-b identical'],
            ['type' => 'noop', 'details' => 'e-c and e-c identical'],
            ['type' => 'noop', 'details' => 'f and f identical'],
        ];
        $this->assertEquals($expected, $operations);
    }

    public function testSimpleFileSizeDifferenceCase()
    {
        $minorChange = $this->createStructure1();
        $minorChange->getContents()[2]->getContents()[1]->setSize(2);
        $operations = $this->runSync($this->createStructure1(), $minorChange);

        // We are specifically interested in one entity
        $this->assertEquals(
            ['type' => 'add', 'details' => 'Copy d-c to dest'],
            $operations[4]
        );
    }

    public function testSimpleSyncCase()
    {
        $operations = $this->runSync($this->createStructure1(), $this->createStructure2());
        $expected = [
            ['type' => 'noop', 'details' => 'a and a identical'],
            ['type' => 'add', 'details' => 'Copy b to dest'],
            ['type' => 'del', 'details' => 'Delete c from dest'],
            ['type' => 'noop', 'details' => 'd and d identical'],
            ['type' => 'noop', 'details' => 'd-a and d-a identical'],
            ['type' => 'del', 'details' => 'Delete d-b from dest'],
            ['type' => 'add', 'details' => 'Copy d-c to dest'],
            ['type' => 'add', 'details' => 'Copy e to dest'],
            ['type' => 'add', 'details' => 'Copy e-a to dest'],
            ['type' => 'add', 'details' => 'Copy e-b to dest'],
            ['type' => 'add', 'details' => 'Copy e-c to dest'],
            ['type' => 'noop', 'details' => 'f and f identical'],
        ];
        $this->assertEquals($expected, $operations);
    }

    public function testSyncCaseSourceIsLonger()
    {
        $source = $this->createStructure1();
        $source->pushObjects([
            new File('/home/person/g')
        ]);
        $operations = $this->runSync($source, $this->createStructure2());
        // We are just interested in the last item
        $this->assertEquals(
            ['type' => 'add', 'details' => 'Copy g to dest', ],
            $operations[12]
        );
    }

    public function testSyncCaseDestIsLonger()
    {
        $dest = $this->createStructure2();
        $dest->pushObjects([
            new File('/home/person/g')
        ]);
        $operations = $this->runSync($this->createStructure1(), $dest);
        // We are just interested in the last item
        $this->assertEquals(
            ['type' => 'del', 'details' => 'Delete g from dest', ],
            $operations[12]
        );
    }

    /**
     * Runs a sync against two in-memory structures, returns the operations list
     *
     * @param array $source
     * @param array $dest
     * @return array
     */
    protected function runSync(Directory $source, Directory $dest)
    {
        $sut = new SyncFiles($this->factory);

        return $sut
            ->setSourceDirectory($source)
            ->setDestinationDirectory($dest)
            ->sync()
            ->getOperations();
    }

    /**
     * @todo These objects fail validation, their parents are not set
     */
    protected function createStructure1(): TestDirectory
    {
        $home = '/home/person';

        // Upper level objects
        $dirD = new TestDirectory($home . '/d');
        $dirD->pushObjects([
            new File($home . '/d-a'),
            new File($home . '/d-c'),
        ]);
        $dirE = new TestDirectory($home . '/e');
        $dirE->pushObjects([
            new File($home . '/e-a'),
            new File($home . '/e-b'),
            new File($home . '/e-c'),
        ]);
        $dirF = new TestDirectory($home . '/f');
        $dirF->setContents([]); // Marks as populated

        // Main level objects
        $dir = $this->createBaseDirectory();
        $dir->pushObjects([
            new File($home . '/a'),
            new File($home . '/b'),
            // Missing file "c"
            $dirD,
            $dirE,
            $dirF,
        ]);

        // Make all file-like objects the same size
        $this->walkStructureAndResetSizes($dir, 1);

        return $dir;
    }

    /**
     * @todo These objects fail validation, their parents are not set
     */
    protected function createStructure2(): TestDirectory
    {
        $home = '/home/person';

        // Upper level objects
        $dirD = new TestDirectory($home . '/d');
        $dirD->pushObjects([
            new File($home . '/d-a'),
            new File($home . '/d-b'),
        ]);
        $dirF = new TestDirectory($home . '/f');
        $dirF->setContents([]); // Marks as populated

        // Main level objects
        $dir = $this->createBaseDirectory();
        $dir->pushObjects([
            new File($home . '/a'),
            // Missing file "b"
            new File($home . '/c'),
            $dirD,
            // Missing dir "e"
            $dirF,
        ]);

        // Make all file-like objects the same size
        $this->walkStructureAndResetSizes($dir, 1);

        return $dir;
    }

    protected function createBaseDirectory(): TestDirectory
    {
        // Injects a factory that always creates test directories :=)
        $dir = new TestDirectory('/home/person');
        $dir->setFactory($this->factory);

        return $dir;
    }

    function walkStructureAndResetSizes(Directory $d, $size) {
        foreach ($d->getContents() as $fsObject) {
            if (method_exists($fsObject, 'hasSize')) {
                $fsObject->setSize($size);
            }
            if ($fsObject instanceof Directory) {
                $this->walkStructureAndResetSizes($fsObject, $size);
            }
        }
    }
}
