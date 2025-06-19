<?php
/*
Plugin Name: A7 Hello Installer
Description: Instala e integra o painel A7 Hello ao tema ativo e permite remover arquivos temporários após a instalação.
Version: 1.1
Author: A7 Sites
*/

register_activation_hook(__FILE__, 'a7_hello_installer_run');

function a7_hello_installer_run() {
    $theme_dir = get_template_directory();
    $functions_file = $theme_dir . '/functions.php';
    $a7_hello_dir = $theme_dir . '/a7-hello';
    $source_dir = plugin_dir_path(__FILE__) . 'a7-hello';

    // 1. Copiar a pasta a7-hello para o tema ativo (se não existir)
    if (!file_exists($a7_hello_dir)) {
        mkdir($a7_hello_dir, 0755, true);
        $dir_iterator = new RecursiveDirectoryIterator($source_dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            $dest = $a7_hello_dir . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                mkdir($dest, 0755, true);
            } else {
                copy($item, $dest);
            }
        }
    }

    // 2. Adicionar o include no functions.php se não existir
    $include_line = "require_once get_template_directory() . '/a7-hello/a7-hello-wp.php';";
    $functions_content = file_get_contents($functions_file);
    if (strpos($functions_content, $include_line) === false) {
        // Adiciona antes do fechamento do PHP, se existir
        if (strpos($functions_content, '?>') !== false) {
            $functions_content = str_replace('?>', "$include_line\n?>", $functions_content);
        } else {
            $functions_content .= "\n$include_line\n";
        }
        file_put_contents($functions_file, $functions_content);
    }

    // 3. Sinaliza sucesso para mostrar aviso
    set_transient('a7_hello_installer_success', true, 60 * 60);
}

// Mensagem de sucesso após ativação, com botão para remover arquivos temporários
add_action('admin_notices', function() {
    if (get_transient('a7_hello_installer_success')) {
        ?>
        <div class="notice notice-success is-dismissible" id="a7-hello-success-msg">
            <p><strong>Painel A7 Hello instalado e integrado ao tema ativo com sucesso!</strong></p>
            <p>Para sua segurança, remova os arquivos temporários do instalador.</p>
            <p>
                <button class="button button-primary" id="a7-hello-remove-installer">Remover arquivos temporários</button>
            </p>
        </div>
        <script>
        document.getElementById('a7-hello-remove-installer').addEventListener('click', function() {
            if(confirm('Tem certeza que deseja remover os arquivos temporários do instalador?')) {
                fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=a7_hello_remove_installer&_wpnonce=' + '<?php echo wp_create_nonce('a7_hello_remove_installer'); ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('a7-hello-success-msg').innerHTML = '<p><strong>Arquivos temporários removidos com sucesso!</strong></p>';
                    } else {
                        alert('Erro ao remover arquivos: ' + data.data);
                    }
                });
            }
        });
        </script>
        <?php
        delete_transient('a7_hello_installer_success');
    }
});

// AJAX handler para remover o plugin instalador
add_action('wp_ajax_a7_hello_remove_installer', function() {
    check_ajax_referer('a7_hello_remove_installer');
    $plugin_file = __FILE__;
    // Desativa e remove o plugin
    deactivate_plugins(plugin_basename($plugin_file));
    if (@unlink($plugin_file)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Não foi possível remover o arquivo do instalador. Remova manualmente via FTP.');
    }
});
