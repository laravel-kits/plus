<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\Plus\FileStorage\Validators;

use function Zhiyi\Plus\setting;
use Zhiyi\Plus\FileStorage\ChannelManager;
use Zhiyi\Plus\FileStorage\Exceptions\NotAllowUploadMimeTypeException;

class CreateTaskValidator extends AbstractValidator
{
    /**
     * Get the validate rules.
     * @return array
     */
    public function rules(bool $image = false): array
    {
        $rules = [
            'filename' => ['bail', 'required', 'string'],
            'hash' => ['bail', 'required', 'string'],
            'size' => ['bail', 'required', 'integer', $this->getAllowMinSize(), $this->getAllowMaxSize()],
            'mime_type' => ['bail', 'required', 'string', $this->getAllowMimeTypes()],
            'storage' => ['bail', 'required', 'array'],
            'storage.channel' => ['bail', 'required', 'string', 'in:'.implode(',', ChannelManager::DRIVES)],
        ];

        if ($image) {
            $rules = array_merge($rules, [
                'dimension' => ['bail', 'required', 'array'],
                'dimension.width' => ['bail', 'required', 'numeric', $this->getAllowImageMinWidth(), $this->getAllowImageMaxWidth()],
                'dimension.height' => ['bail', 'required', 'numeric', $this->getAllowImageMinHeight(), $this->getAllowImageMaxHeight()],
            ]);
        }

        return $rules;
    }

    /**
     * Get image allow min width.
     * @return string
     */
    protected function getAllowImageMinWidth(): string
    {
        return sprintf('min:%d', setting('core', 'file:upload-allow-image-min-width', 0));
    }

    /**
     * Get image allow max width.
     * @return string
     */
    protected function getAllowImageMaxWidth(): string
    {
        return sprintf('max:%d', setting('core', 'file:upload-allow-image-max-width', 0));
    }

    /**
     * Get image allow min height.
     * @return string
     */
    protected function getAllowImageMinHeight(): string
    {
        return sprintf('min:%d', setting('core', 'file:upload-allow-image-min-height', 0));
    }

    /**
     * Get image allow max height.
     * @return string
     */
    protected function getAllowImageMaxHeight(): string
    {
        return sprintf('max:%d', setting('core', 'file:upload-allow-image-max-height', 0));
    }

    /**
     * Get allow min file size.
     * @return string
     */
    protected function getAllowMinSize(): string
    {
        return sprintf('min:%d', setting('core', 'file:upload-allow-min-size', 0));
    }

    /**
     * Get allow max file size.
     * @return string
     */
    protected function getAllowMaxSize(): string
    {
        return sprintf('max:%d', setting('core', 'file:upload-allow-max-size', 2097152));
    }

    /**
     * Get allow mime types.
     * @return string
     */
    protected function getAllowMimeTypes(): string
    {
        $mimeTypes = setting('core', 'file:upload-allow-mime-types', ['image/png']);
        if (empty($mimeTypes)) {
            throw new NotAllowUploadMimeTypeException();
        }

        return sprintf('in:%s', implode(',', $mimeTypes));
    }
}
