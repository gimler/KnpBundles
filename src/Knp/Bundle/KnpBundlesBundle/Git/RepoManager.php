<?php

namespace Knp\Bundle\KnpBundlesBundle\Git;

use Knp\Bundle\KnpBundlesBundle\Entity\Repo as RepoEntity;
use Symfony\Component\HttpKernel\Util\Filesystem;
use PHPGit_Repository;

class RepoManager
{
    /**
     * Repositories base directory
     *
     * @var string
     */
    protected $dir = null;

    protected $filesystem = null;

    /**
     * git executable
     *
     * @var string
     */
    protected $gitExecutable;

    public function __construct($dir, $gitExecutable)
    {
        $this->dir = $dir;
        $this->gitExecutable = $gitExecutable;
        $this->filesystem = new Filesystem();

        $this->filesystem->mkdir($this->dir);
    }

    public function getRepo(RepoEntity $repo)
    {
        if($this->hasRepo($repo)) {
            $dir = $this->getRepoDir($repo);
            $gitRepo = new PHPGit_Repository($dir, false, array('git_executable' => $this->gitExecutable));
        } else {
            $gitRepo = $this->createGitRepo($repo);
        }

        return new Repo($repo, $gitRepo);
    }

    public function hasRepo(RepoEntity $repo)
    {
        $dir = $this->getRepoDir($repo);

        return is_dir($dir.'/.git');
    }

    public function createGitRepo(RepoEntity $repo)
    {
        $dir = $this->getRepoDir($repo);
        $this->filesystem->mkdir($dir);
        $gitRepo = PHPGit_Repository::cloneUrl($dir, $repo->getGitUrl(), false, array('git_executable' => $this->gitExecutable));

        return $gitRepo;
    }

    public function getRepoDir(RepoEntity $repo)
    {
        return $this->dir.'/'.$repo->getUsername().'/'.$repo->getName();
    }

    /**
     * Get dir
     *
     * @return string
     */
    public function getDir()
    {
      return $this->dir;
    }

    /**
     * Set dir
     *
     * @param  string
     * @return null
     */
    public function setDir($dir)
    {
      $this->dir = $dir;
    }
}
