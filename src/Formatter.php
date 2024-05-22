<?php

namespace Vortechstudio\LaravelTestGenerator;

class Formatter
{
    protected array $cases;
    protected string $file;
    protected string $namespace;
    protected string $destinationFilePath;
    protected string $directory;
    protected string $sync;

    public function __construct($directory, $sync)
    {
        $this->directory = $directory;
        $this->sync = $sync;
        $this->file = __DIR__.'/Test/UserTest.php';
        $this->namespace = 'namespace Tests\Feature' . ($this->directory ? '\\' . $this->directory : '') . ';';
        $this->destinationFilePath = base_path('tests/Feature/' . $this->directory);
        $this->cases = [];
    }

    /**
     * Formats the given case and adds it to the cases array.
     *
     * @param array $case The case to be formatted.
     * @param string $url The URL of the case.
     * @param string $method The HTTP method of the case.
     * @param string $controllerName The name of the controller.
     * @param string $actionName The name of the action.
     * @param mixed $auth The authentication data for the case.
     * @return void
     */
    public function format(array $case, string $url, string $method, string $controllerName, string $actionName, $auth): void
    {
        $this->cases[$controllerName]['action'] = $actionName;
        $this->cases[$controllerName]['url'] = $url;
        $this->cases[$controllerName]['method'] = $method;
        $this->cases[$controllerName]['params'] = $case;
        $this->cases[$controllerName]['auth'] = $auth;
        if(empty($this->cases[$controllerName]['function'])) {
            $this->cases[$controllerName]['function'] = [];
        }
        $this->formatFunction($controllerName);
    }

    /**
     * Generate the directory and format the file.
     *
     * @return void
     */
    public function generate(): void
    {
        $this->createDirectory();
        $this->formatFile();
    }

    /**
     * Format the function for a given controller.
     *
     * @param string $controllerName The name of the controller.
     * @return void
     */
    protected function formatFunction(string $controllerName): void
    {
        $functionName = '';
        $i = 0;
        $controller = $this->cases[$controllerName];

        foreach ($controller['params'] as $index => $item) {
            # Add function documentation
            $function = "\t" . '/**' . PHP_EOL . "\t" . ' * ' . $controller['action'] . PHP_EOL . "\t" . ' *' . PHP_EOL;

            # Check @depends to be added or not
            if($this->sync) {
                if($i > 0) {
                    $function .= "\t" . ' * @depends ' . $functionName . PHP_EOL;
                } else {
                    if(count($controller['function']) > 0) {
                        $function .= "\t" . ' * @depends ' . end($controller['function'])['name'] . PHP_EOL;
                    }
                }
            }

            $function .= "\t" . ' * @return void' . PHP_EOL . "\t" . ' */' . PHP_EOL;
            $functionName = $this->getFunctionName($index, $controller['action']);

            # Function name and declaration
            $function .= "\t" . 'public function ' . $functionName . '()';

            # Function definition
            $body = "\t\t".'$response = $this->json(\'' . strtoupper($controller['method']) . '\', \'' . $controller['url'] . '\', [';

            # Request parameters
            $params = $this->getParams($item);
            $body .= $params ? PHP_EOL . $params . PHP_EOL . "\t\t". ']' : ']';

            $body .= $controller['auth'] ? ", [\n\t\t\t'Authorization' => 'Bearer '\n\t\t]" : '';

            $body .= ');';
            # Assert response
            $body .= PHP_EOL . PHP_EOL . "\t\t" . '$response->assertStatus(' . ($index == 'failure' ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK) . ');' . PHP_EOL;

            # Add the function to the global array
            $this->cases[$controllerName]['function'][] = [
                'name' => $functionName,
                'code' => $function . PHP_EOL . "\t" . '{' . PHP_EOL . $body . PHP_EOL . "\t" . '}' . PHP_EOL
            ];

            $i++;
        }
    }

    /**
     * Format the file.
     *
     * @return void
     */
    protected function formatFile(): void
    {
        foreach ($this->cases as $key => $value) {
            $lines = file($this->file, FILE_IGNORE_NEW_LINES);
            $lines[2] = $this->namespace;
            $lines[8] = $this->getClassName($key, $lines[8]);
            $functions = implode(PHP_EOL, Arr::pluck($value['function'], 'code'));
            $content = array_merge(array_slice($lines, 0, 10) , [$functions] , array_slice($lines, 11));

            $this->writeToFile($key . 'Test', $content);
        }
    }

    /**
     * Write content to a file.
     *
     * @param string $controllerName The name of the controller.
     * @param array $content The content to be written to the file.
     *
     * @return void
     */
    protected function writeToFile(string $controllerName, array $content): void
    {
        $fileName = $this->destinationFilePath . '/' . $controllerName . '.php';
        $file = fopen($fileName, 'w');
        foreach ($content as $index => $value) {
            fwrite($file, $value.PHP_EOL);
        }
        fclose($file);

        echo "\033[32m". basename($fileName). ' Created Successfully'. PHP_EOL;
    }

    /**
     * Get the class name.
     *
     * @param string $controllerName The name of the controller.
     * @param string $line The line to replace the class name.
     *
     * @return array|string The modified line with the updated class name.
     */
    protected function getClassName(string $controllerName, string $line): array|string
    {
        return str_replace('UserTest', $controllerName . 'Test', $line);
    }

    /**
     * Get the formatted parameter(s) for the given value.
     *
     * @param mixed $param The parameter value.
     *
     * @return array|string The formatted parameter(s) or an empty string if parameter is empty.
     */
    protected function getParams(mixed $param): array|string
    {
        if(empty($param)) {
            return '';
        }
        $param = json_encode($param);
        $param = str_replace(['{', '}'], '', $param);
        $param = "\t\t\t".$param;
        $param = str_replace('":', '" => ', $param);
        return str_replace(',', ",\n\t\t\t", $param);
    }

    /**
     * Gets the function name based on the index and action.
     *
     * @param string $index The index string.
     * @param string $action The action string.
     * @return string The generated function name.
     */
    protected function getFunctionName(string $index, string $action): string
    {
        $name = 'test' . $action;
        return $index == 'failure' ? $name . 'WithError' : $name;
    }

    /**
     * Creates a directory if it does not already exist.
     *
     * @return void
     */
    protected function createDirectory(): void
    {
        $dirName = $this->destinationFilePath;
        if(!is_dir($dirName)) {
            mkdir($dirName, 0755, true);
        }
    }
}
