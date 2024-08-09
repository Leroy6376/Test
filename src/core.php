<?php

class Dictionary
{
    private $handle = null;
    private $array = [];

    public function openFile(string $fileName): bool
    {
        $this->handle = fopen($fileName, "r");
        if ($this->handle) {
            return true;
        }
        return false;
    }

    public function processFile(): bool
    {
        while (($buffer = fgets($this->handle, 1024)) !== false) {
            $letter = mb_strtolower(mb_substr(preg_replace('/[^ a-zа-яё\d]/ui', '', $buffer), 0, 1));
            if ($letter === '') {
                return false;
            }
            if (array_key_exists($letter, $this->array)) {
                $this->array[$letter]['count'] += substr_count(mb_strtolower($buffer), $letter);
            } else {
                $this->array[$letter]['count'] = substr_count(mb_strtolower($buffer), $letter);
                if (!file_exists("library/$letter")) {
                    if (!mkdir("library/$letter", 0700)) {
                        return false;
                    }
                }
                $this->array[$letter]['handle'] = fopen("library/$letter/words.txt", "w");
                if (!$this->array[$letter]['handle']) {
                    return false;
                }
            }
            if (!fwrite($this->array[$letter]['handle'], $buffer)) {
                return false;
            }
        }
        return true;
    }

    public function writeWordsCount(): bool
    {
        foreach ($this->array as $key => $value) {
            fclose($value['handle']);
            $handle = fopen("library/$key/count.txt", "w");
            if (!$handle) {
                return false;
            }
            if (!fwrite($handle, $value['count'])) {
                return false;
            }
            fclose($handle);
        }
        fclose($this->handle);
        return true;
    }

    public function clearDir(): void
    {
        if (file_exists("library")) {
            $this->rmRec("library");
            mkdir("library");
        }
    }

    private function rmRec($path): bool
    {
        if (is_file($path)) {
            return unlink($path);
        }
        if (is_dir($path)) {
            foreach (scandir($path) as $p) {
                if (($p != '.') && ($p != '..')) {
                    $this->rmRec($path . DIRECTORY_SEPARATOR . $p);
                }
            }
            return rmdir($path);
        }
        return false;
    }
}
