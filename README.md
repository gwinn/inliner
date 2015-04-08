#Inliner

Simple class for combining several classes in a single file. Can be used for keep compability with php 5.2

### Example

```php
$inliner = new Inliner();
$inliner->inline(__DIR__ . '/src', 'php', 'single.php');
```
