<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['app', 'database', 'routes', 'tests']) // Define as pastas onde o código será analisado
    ->exclude(['storage', 'vendor', 'bootstrap/cache']) // Exclui pastas desnecessárias
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true) // Permite regras "arriscadas" que podem alterar o comportamento do código
    ->setUsingCache(false) // Desativa o cache para sempre rodar a análise do zero
    ->setRules([
        '@Symfony' => true, // Usa regras do padrão Symfony
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'], // Usa `[]` ao invés de `array()`
        'binary_operator_spaces' => ['operators' => ['=>' => null]], // Mantém espaços entre operadores `=>`
        'combine_consecutive_issets' => true, // Junta múltiplas chamadas `isset`
        'concat_space' => ['spacing' => 'one'], // Define espaço entre concatenação de strings (`.`)
        'fully_qualified_strict_types' => false, // Não força `use` para classes internas do PHP
        'linebreak_after_opening_tag' => true, // Pula uma linha após `<?php`
        'mb_str_functions' => true, // Converte funções de string para `mb_*`
        'no_null_property_initialization' => true, // Remove inicializações desnecessárias com `null`
        'no_superfluous_elseif' => true, // Converte `elseif` desnecessário para `else`
        'no_superfluous_phpdoc_tags' => false, // Mantém tags de PHPDoc que não são estritamente necessárias
        'no_useless_else' => true, // Remove `else` redundantes
        'no_useless_return' => true, // Remove `return;` desnecessários
        'not_operator_with_space' => false, // Mantém `!` grudado na variável
        'ordered_class_elements' => true, // Ordena os elementos da classe (métodos, propriedades)
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Ordena `use` em ordem alfabética
        'phpdoc_add_missing_param_annotation' => true, // Adiciona anotações `@param` ausentes
        'phpdoc_order' => true, // Ordena as anotações do PHPDoc
        'phpdoc_trim_consecutive_blank_line_separation' => true, // Remove linhas em branco extras no PHPDoc
        'return_assignment' => true, // Converte `$var = algo; return $var;` para `return algo;`
        'simplified_null_return' => true, // Converte `return null;` para `return;`
        'ternary_to_null_coalescing' => true, // Converte ternário simples para `??`
        'yoda_style' => false, // Desativa Yoda Conditions (`if (42 == $var)`)
    ]);
