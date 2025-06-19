<?php
// Arquivo de funções personalizadas para o tema Hello Elementor

// Exemplo de função personalizada:
function a7_hello_wp_exemplo() {
    // Seu código personalizado aqui
}

// Remove o painel padrão de boas-vindas
function remover_widget_boas_vindas() {
    remove_action('welcome_panel', 'wp_welcome_panel');
}
add_action('admin_init', 'remover_widget_boas_vindas');

// Remove o botão de dispensar do painel de boas-vindas
function a7_hello_wp_remove_welcome_panel_dismiss() {
    echo '<style>.welcome-panel-close { display: none !important; }</style>';
}
add_action('admin_head', 'a7_hello_wp_remove_welcome_panel_dismiss');

// Remove a aba "Opções de Tela" do admin
function a7_hello_wp_remove_screen_options() {
    echo '<style>#screen-options-link-wrap { display: none !important; }</style>';
}
add_action('admin_head', 'a7_hello_wp_remove_screen_options');

// Remove a aba "Ajuda" do admin
function a7_hello_wp_remove_help_tab() {
    echo '<style>#contextual-help-link-wrap { display: none !important; }</style>';
}
add_action('admin_head', 'a7_hello_wp_remove_help_tab');

// Inclui Bootstrap 4.6 e o CSS personalizado apenas no painel administrativo
function a7_hello_wp_admin_enqueue_bootstrap($hook) {
    // Só carrega no painel principal do admin
    if ($hook !== 'index.php') {
        return;
    }
    // CSS do Bootstrap 4.6
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
        array(),
        '4.6.2'
    );
    // CSS personalizado
    wp_enqueue_style(
        'a7-style',
        get_template_directory_uri() . '/a7-hello/style-a7.css',
        array('bootstrap-css'),
        filemtime(get_template_directory() . '/a7-hello/style-a7.css')
    );
    // JS do Bootstrap 4.6
    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        '4.6.2',
        true
    );
}
add_action('admin_enqueue_scripts', 'a7_hello_wp_admin_enqueue_bootstrap');

// Adiciona painel de boas-vindas personalizado
function painel_boas_vindas_personalizado() {
    // Definir timezone para o horário de Brasília
    date_default_timezone_set('America/Sao_Paulo');

    // Incluir aviso do painel, se existir
    $aviso_painel_path = get_template_directory() . '/a7-hello/extras/dados/aviso-painel.php';
    if (file_exists($aviso_painel_path)) {
        include $aviso_painel_path;
    }
    if (!empty($aviso_painel_titulo) && !empty($aviso_painel_mensagem)) {
        ?>
        <div class="modal fade" id="avisoPainelModal" tabindex="-1" role="dialog" aria-labelledby="avisoPainelLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="padding:30px; text-align:center;">
              <div class="modal-header border-0">
                <h5 class="modal-title w-100" id="avisoPainelLabel"><?php echo esc_html($aviso_painel_titulo); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <?php echo wpautop(esc_html($aviso_painel_mensagem)); ?>
              </div>
            </div>
          </div>
        </div>
        <script>
        jQuery(document).ready(function($){
            $('#avisoPainelModal').modal('show');
        });
        </script>
        <?php
    }

    // Inclui os dados dinâmicos do painel
    $info_wp_path = get_template_directory() . '/a7-hello/extras/dados/info-wp.php';
    if (file_exists($info_wp_path)) {
        require_once $info_wp_path;
    } else {
        echo '<div style="color:red">Arquivo info-wp.php não encontrado!</div>';
    }

    $info_dns1 = isset($info_dns1) ? $info_dns1 : 'DNS1 não definido';
    $info_dns2 = isset($info_dns2) ? $info_dns2 : 'DNS2 não definido';
    $info_vencimento = isset($info_vencimento) ? $info_vencimento : 'Vencimento não definido';
    $info_forma_pagamento = isset($info_forma_pagamento) ? $info_forma_pagamento : 'Forma de pagamento não definida';
    $info_plano = isset($info_plano) ? $info_plano : 'Plano não definido';
    $info_analitics = isset($info_analitics) ? $info_analitics : 'Analytics não definido';
    $info_dados_plano = isset($info_dados_plano) ? $info_dados_plano : 'Dados do plano não definidos';
    $info_analitics_link = isset($info_analitics_link) ? $info_analitics_link : '#';
    $info_dados_plano_link = isset($info_dados_plano_link) ? $info_dados_plano_link : '#';
    $info_pix_cnpj = isset($info_pix_cnpj) ? $info_pix_cnpj : '';
    $info_pix_qrcode_url = isset($info_pix_qrcode_url) ? $info_pix_qrcode_url : '';
    $info_pix_qrcode_img = isset($info_pix_qrcode_img) ? $info_pix_qrcode_img : '';
    $current_user = wp_get_current_user();
    $nome = $current_user->user_firstname ? $current_user->user_firstname : $current_user->display_name;
    $sobrenome = $current_user->user_lastname;
    $dia = date_i18n('d');
    $mes = date_i18n('F');
    $ano = date_i18n('Y');
    $hora = date_i18n('H:i');
    ?>
    <div class="welcome-panel-content">

        <div class="content-header">
            <blockquote class="blockquote">
                <h2>Bem-vindo(a) ao Painel WordPress da A7 Sites!</h2>
            </blockquote>
            <p class="about-description">
                Olá, <strong><?php echo esc_html($nome . ' ' . $sobrenome); ?></strong>, hoje é <?php echo esc_html($dia); ?> de <?php echo esc_html($mes); ?> de <?php echo esc_html($ano); ?>, agora são <?php echo esc_html($hora); ?>.
            </p>
        
            <div class="info-panel">
                <div class="row">
                        <div class="col-md-4">
                            <div class="info-panel-item">
                                <h3><span class="info-panel-icon-circle"><?php echo file_get_contents(get_template_directory() . '/a7-hello/extras/icons/1748702299-rocket.svg'); ?></span>Dados da Hospedagem</h3>
                                <p>Servidor DNS:</p>
                                <span class="badge badge-primary"><?php echo esc_html($info_dns1); ?></span>
                                <span class="badge badge-primary"><?php echo esc_html($info_dns2); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-panel-item">
                                <h3><span class="info-panel-icon-circle"><?php echo file_get_contents(get_template_directory() . '/a7-hello/extras/icons/1748702028-calendar.svg'); ?></span>Vencimento</h3>
                                <p>Proximo Pagamento:</p>
                                <span class="badge badge-success"><?php echo esc_html($info_vencimento); ?></span>
                                <a href="#" class="badge badge-warning" data-toggle="modal" data-target="#pixModal"><?php echo esc_html($info_forma_pagamento); ?></a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-panel-item">
                                <h3><span class="info-panel-icon-circle"><?php echo file_get_contents(get_template_directory() . '/a7-hello/extras/icons/1748702395-cloud-network.svg'); ?></span>Plano Contratado</h3>
                                <p>Plano Contratado:</p>
                                <span class="badge badge-primary"><?php echo esc_html($info_plano); ?></span>
                                <a href="<?php echo esc_url($info_analitics_link); ?>" class="badge badge-danger"><?php echo esc_html($info_analitics); ?></a>
                                <a href="<?php echo esc_url($info_dados_plano_link); ?>" class="badge badge-light"><?php echo esc_html($info_dados_plano); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        
        <div class="welcome-panel-column-container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Meu WhatsApp</h5>
                            <p class="card-text">Fale diretamente com os contatos capturados pelo WhatsApp para dúvidas, sugestões ou solicitações rápidas.</p>
                            <a href="/wp-admin/admin.php?page=a7w-whatsapp" class="btn btn-success">Veja o recurso...</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">A7 Cloud Analitics</h5>
                            <p class="card-text">Gerencie e visualize métricas de performance, acessos de todos os dados do seu site, segurança e cache do seu site com a integração CloudFlare.</p>
                            <a href="/wp-admin/admin.php?page=a7_cloudflare_metrics" class="btn btn-warning">Veja o recurso...</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body"><h5>Sua Hospedagem A7</h5>
                            <p class="card-text">Acesse informações de dos E-mails, o consumo de disco SSD, o consumo de banda, estruções de como configurar Outlook e os Banco MySql.</p>
                            <a href="#" class="btn btn-primary">Veja o recurso...</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Suporte</h5>
                            <p class="card-text">Precisa de ajuda? Fale com o suporte da A7 Sites. Somos especializados em resolver qualquer problema que possa surgir.</p>
                            <a href="https://api.whatsapp.com/send?phone=5531995999029" target="_blank" class="btn btn-info">Fale conosco</a>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Modal PIX -->
    <div class="modal fade" id="pixModal" tabindex="-1" role="dialog" aria-labelledby="pixModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="text-align:center;">
          <div class="modal-header border-0">
            
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <img src="<?php echo esc_url($info_pix_qrcode_img); ?>" alt="QR Code PIX" style="margin-bottom:20px;max-width:100%;height:auto;" />
            
          </div>
        </div>
      </div>
    </div>
    <?php
}
add_action('welcome_panel', 'painel_boas_vindas_personalizado');

