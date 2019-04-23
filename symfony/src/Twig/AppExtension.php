<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/19/19
 * Time: 3:11 AM.
 */

namespace App\Twig;

use App\Entity\Post;
use App\Entity\PostLikedByUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('merge_recursive', [$this, 'mergeRecursive']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('generate_all_input_hidden', [$this, 'generateInputHidden']),
            new TwigFunction('is_post_liked_by_user', [$this, 'isPostLikedByUser']),
        ];
    }

    public function mergeRecursive(array $all, array $changes): array
    {
        foreach ($changes as $key => $value) {
            if (\is_array($value)) {
                $all[$key] = $this->mergeRecursive($all[$key], $value);
            } else {
                $all[$key] = $value;
            }
        }

        return $all;
    }

    public function generateInputHidden(array $all, array $exclude = [])
    {
        $html = '';
        foreach (array_diff_key($all, array_flip($exclude)) as $key => $value) {
            if (\is_array($value)) {
                $html .= $this->generateInputHidden($this->formatKeysInArray($value, $key . '[%s]'));
            } else {
                $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
            }
        }

        return $html;
    }

    public function isPostLikedByUser(Post $post, User $user): bool
    {
        return (bool) $this
            ->entityManager
            ->getRepository(PostLikedByUser::class)
            ->findOneByPostAndUser($post, $user);
    }

    private function formatKeysInArray(array $array, $format)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[sprintf($format, $key)] = $value;
        }

        return $result;
    }
}
