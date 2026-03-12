<?php

namespace Vis\Builder\Http\Traits;

trait SeoTrait
{
    public function getSeoTitle(): string
    {
        if ($this->seo && $this->seo->t('seo_title')) {
            return strip_tags($this->seo->t('seo_title'));
        }

        if ($this->t('title')) {
            return strip_tags($this->t('title')) . ' ' . __t('title_seo_end');
        }

        return '';
    }

    public function getSeoDescription(): string
    {
        if ($this->seo && $this->seo->t('seo_description')) {
            return strip_tags($this->seo->t('seo_description'));
        }

        if ($this->t('short_description')) {
            return strip_tags($this->t('short_description')) . ' ' . __t('title_seo_end');
        }

        return '';
    }

    public function getSeoText(): string
    {
        if ($this->seo && $this->seo->t('seo_text')) {
            return $this->seo->t('seo_text');
        }

        return '';
    }

    public function getSeoPicture(): string
    {
        if ($this->picture) {
            return $this->getImgPath(600, 314);
        }

        return asset(glide(setting('seo_logo'), ['w' => 600, 'h' => 314]));
    }

    public function loadSeo()
    {
        return $this::load('seo')->rememberForever()->cacheTags('tb_tree');
    }
}
