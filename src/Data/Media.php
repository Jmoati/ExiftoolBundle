<?php

declare(strict_types=1);

namespace Jmoati\ExifTool\Data;

use DateTime;

class Media
{
    /** @var array */
    private $data = [];

    /** @var DateTime|null */
    private $date;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        unset($data['SourceFile'], $data['ExifTool'], $data['File']['FileName'], $data['File']['FilePermissions'], $data['File']['Directory'], $data['File']['FileModifyDate'], $data['File']['FileAccessDate'], $data['File']['FileInodeChangeDate']);

        $this->data = $data;
    }

    /**
     * @param array $data
     *
     * @return Media
     */
    public static function create(array $data): self
    {
        return new static($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        if (null === $this->date) {
            $date = null;

            foreach ($this->data as $data) {
                foreach (['DateTimeOriginal', 'CreationDate', 'DateCreated'] as $key) {
                    if (isset($data[$key])) {
                        try {
                            $now = new DateTime();
                            $date = new DateTime($data[$key]);
                            $string = $date->format('Y-m-d H:i:s');

                            if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $string)) {
                                if ($string == $now->format('Y-m-d H:i:s')) {
                                    $date = null;
                                } else {
                                    break 2;
                                }
                            } else {
                                $date = null;
                            }
                        } catch (\Exception $e) {
                            $date = null;
                        }
                    }
                }
            }

            $this->date = $date;
        }

        return $this->date;
    }
}
