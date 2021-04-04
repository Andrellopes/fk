<?php

include_once _PS_MODULE_DIR_.'fkcorreiosg2cp2/tcpdf/tcpdf.php';
include_once _PS_MODULE_DIR_.'fkcorreiosg2cp2/defines/defines.php';

class GerarEtiqEnderClass extends Order {

    public $fk_etiq_ender;

    public function __construct($id = null, $id_lang = null) {

        $this->context = Context::getContext();

        self::$definition['fields']['fk_etiq_ender'] = array('type' => self::TYPE_BOOL,);
        parent::__construct($id);
    }

    public function getFields(){

        $add_field = parent::getFields();
        $add_field['fk_etiq_ender'] = pSQL($this->fk_etiq_ender);

        return $add_field;
    }

    public function geraEtiquetas_2($pedidos) {

        if (!$pedidos) {
            return false;
        }

        // Nome do PDF
        $nomePdf = 'etiquetaEnder_'.uniqid().'.pdf';

        // Data PDF
        $dataCriacaoPdf = date('Y-m-d H:i:s');

        if (!is_array($pedidos)) {
            $pedidos = array('0' => $pedidos);
        }

        // Instancia TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Informacoes do documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Fokusfirst');
        $pdf->SetTitle('Etiquetas de Endereçamento');

        // Margens
        $marginLeft = 5;
        $marginTop = 5;
        $marginRight = 5;
        $pdf->SetMargins($marginLeft, $marginTop, $marginRight);

        // Remove header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Fator de redimensionamento de imagens
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $styleBarCodeDM = array(
            'border' => false,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        );

        $styleBarCode128 = array(
            'stretch' => false,
            'fitwidth' => true,
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false,
        );

        $styleRetangulo = array(
            'width' => 0.5,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => array(0, 0, 0)
        );

        $styleFlexa = array(
            'width' => 0.25,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => array(0, 0, 0)
        );

        $styleLinhaRemetente = array(
            'width' => 0.3,
            'cap' => 'butt',
            'join' => 'miter',
            'color' => array(225, 225, 225)
        );

        $pedidosImpressos = array();
        $etiqImpressa = 1;

        foreach ($pedidos as $pedido) {

            // Variaveis de posicionamento
            $posRetangulo_x = 18;
            $posRetangulo_y = 17;

            $posLogo_x = 157;
            $posLogo_y = 17;

            $posBarCodeDM_x = 155;
            $posBarCodeDM_y = 27;

            $cellMarginLeft = 12;

            $posBarCode128_x = 82;
            $posBarCode128_y = 74;

            $posLinhaRemetente_x = 6;
            $posLinhaRemetente_y = 104;

            if ($etiqImpressa == 1) {
                // adiciona pagina
                $pdf->AddPage();
            }else {
                $posRetangulo_y += 144;
                $posLogo_y += 144;
                $posBarCodeDM_y += 144;
                $posBarCode128_y += 144;
                $posLinhaRemetente_y += 144;

                $etiqImpressa = 0;
            }

            // Variaveis de posicionamento
            $posLinhaRemetente_x1 = $posLinhaRemetente_x + 197; //176;
            $posLinhaRemetente_y1 = $posLinhaRemetente_y;

            // Recupera dados do pedido
            $order = new Order($pedido);

            // Recupera dados do cadastro e endereco do cliente
            $dadosCliente = $this->recuperaEndereco($order->id_address_delivery);

            // CEP do destinatario
            $cepDestNaoFormatado = trim(preg_replace("/[^0-9]/", "", $dadosCliente['postcode']));

            if (strlen($cepDestNaoFormatado) == 8) {
                $cepDestFormatado = substr($cepDestNaoFormatado, 0, 5).'-'.substr($cepDestNaoFormatado, 5, 3);
            }

            // CEP do Remetente
            $cepRemNaoFormatado = trim(preg_replace("/[^0-9]/", "", trim(Configuration::get('FKCORREIOSG2CP2_CEP'))));
            $cepRemFormatado = substr($cepRemNaoFormatado, 0, 5).'-'.substr($cepRemNaoFormatado, 5, 3);

            // Numero Destinatario
            $numEnd = false;
            $numeroDest = '';

            if (module::isInstalled('fkcustomers')) {
                if (isset($dadosCliente['numend'])) {
                    if (trim($dadosCliente['numend']) != '') {
                        if (!Configuration::get('FKCUSTOMERS_MODO') or Configuration::get('FKCUSTOMERS_MODO') == '1') {

                            $numEnd = true;
                            $numeroDest = trim($dadosCliente['numend']);

                        }
                    }
                }
            }

            // Numero Remetente
            $numeroRem = trim(Configuration::get('FKCORREIOSG2CP2_NUMERO'));

            // reset na margem da celula
            $pdf->setCellMargins(0, 0, 0, 0);

            // Retangulo
            $pdf->SetLineStyle($styleRetangulo);

            $largura = 38;
            $altura = 6;
            $pdf->RoundedRect($posRetangulo_x, $posRetangulo_y, $largura, $altura, 1, '1111', 'DF', '',array(255, 255, 255));

            // Flexa
            $pdf->SetLineStyle($styleFlexa);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->Arrow($posRetangulo_x + 5, $posRetangulo_y + 8, $posRetangulo_x + 5, $posRetangulo_y + 11, 2, 5, 35);

            // Texto Destinatario
            $pdf->SetFont('helvetica','B', 10);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->Text($posRetangulo_x + 5, $posRetangulo_y + 1, $this->convUpper('Destinatário'));

            // Logo
            if (file_exists(FK_URI_IMG.'logo_2.jpg')) {
                $larguraImg = 26.46;
                $alturaImg = 6.61;
                $pdf->Image(FK_URI_IMG.'logo_2.jpg', $posLogo_x, $posLogo_y, $larguraImg, $alturaImg, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }

            // Codigo de barras Data Matrix
            $pdf->write2DBarcode($cepDestNaoFormatado, 'DATAMATRIX', $posBarCodeDM_x, $posBarCodeDM_y, 16, 16, $styleBarCodeDM, 'N');
            $numTmp = '00000';

            if (is_numeric($numeroDest)) {
                $numTmp = substr($numTmp.$numeroDest, -5);
            }
            $pdf->write2DBarcode($numTmp, 'DATAMATRIX', $posBarCodeDM_x + 16, $posBarCodeDM_y, 16, 16, $styleBarCodeDM, 'N');

            $pdf->write2DBarcode($cepRemNaoFormatado, 'DATAMATRIX', $posBarCodeDM_x, $posBarCodeDM_y + 16, 16, 16, $styleBarCodeDM, 'N');
            $numTmp = '00000';

            if (is_numeric($numeroRem)) {
                $numTmp = substr($numTmp.$numeroRem, -5);
            }
            $pdf->write2DBarcode($numTmp, 'DATAMATRIX', $posBarCodeDM_x + 16, $posBarCodeDM_y + 16, 16, 16, $styleBarCodeDM, 'N');

            // Fonte
            $pdf->SetFont('helvetica', '', 10);

            // margem da celula
            $pdf->setCellMargins($cellMarginLeft, 0, 0, 0);

            // Altera coordenada
            $pdf->SetAbsY($posRetangulo_y + 13);

            // Largura e Altura da celula
            $larguraCelula = 110;

            // Nome
            $nome = trim($dadosCliente['firstname']).' '.trim($dadosCliente['lastname']);
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($nome), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Endereco
            if ($numEnd) {
                $endereco = trim($dadosCliente['address1']).' '.$numeroDest;
            }else {
                $endereco = trim($dadosCliente['address1']);
            }

            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($endereco), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Complemento
            if (isset($dadosCliente['compl'])) {

                $tmp = trim($dadosCliente['compl']);

                if (!empty($tmp)) {
                    $complemento = $tmp;
                    $pdf->MultiCell($larguraCelula, 0, $this->convUpper($complemento), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);
                }
            }

            // Bairro
            $tmp = trim($dadosCliente['address2']);

            if (!empty($tmp)) {
                $bairro = $tmp;
                $pdf->MultiCell($larguraCelula, 0, $this->convUpper($bairro), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);
            }

            // Cidade e Estado
            $cidade = trim($dadosCliente['city']);
            $estado = trim($dadosCliente['iso_code']);
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($cidade.' / '.$estado), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // CEP
            if (strlen($cepDestNaoFormatado) == 8) {

                $pdf->MultiCell($larguraCelula, 0, $cepDestFormatado, 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

                // reset na margem da celula
                $pdf->setCellMargins(0, 0, 0, 0);

                // Codigo de barras 128
                $pdf->write1DBarcode($cepDestNaoFormatado, 'C128C', $posBarCode128_x, $posBarCode128_y, '', 25, 0.5, $styleBarCode128, 'N');

                // CEP abaixo do codigo de barras
                $pdf->SetFont('helvetica', '', 8);

                $pdf->Text($posBarCode128_x + 16, $posBarCode128_y + 23, $cepDestFormatado);
            }

            // Linha remetente
            $pdf->Line($posLinhaRemetente_x, $posLinhaRemetente_y, $posLinhaRemetente_x1, $posLinhaRemetente_y1, $styleLinhaRemetente);

            $pdf->SetFont('helvetica', 'B', 7);

            $pdf->Text($posLinhaRemetente_x + 6, $posLinhaRemetente_y + 1, $this->convUpper('Remetente:'));

            $pdf->SetFont('helvetica', '', 7);

            // Pedido e referencia
            $pdf->Text($posLinhaRemetente_x + 145, $posLinhaRemetente_y + 1, $this->convUpper('Pedido: '.$order->id.' ('.$order->reference.')'));

            // reset na margem da celula
            $pdf->setCellMargins(0, 0, 0, 0);

            // Largura e Altura da celula
            $larguraCelula = 150;

            // Nome remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 6);
            $pdf->SetAbsY($posLinhaRemetente_y + 4);
            $nome = trim(Configuration::get('FKCORREIOSG2CP2_REMETENTE'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($nome), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Endereco remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 6);
            $endereco = trim(Configuration::get('FKCORREIOSG2CP2_ENDERECO')).' '.trim(Configuration::get('FKCORREIOSG2CP2_NUMERO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($endereco), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Bairro remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 6);
            $bairro = trim(Configuration::get('FKCORREIOSG2CP2_BAIRRO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($bairro), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Cidade e Estado do remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 6);
            $cidade = trim(Configuration::get('FKCORREIOSG2CP2_CIDADE'));
            $estado = trim(Configuration::get('FKCORREIOSG2CP2_ESTADO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($cidade.' / '.$estado), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // CEP do destinatario
            $pdf->SetAbsX($posLinhaRemetente_x + 6);
            $pdf->MultiCell($larguraCelula, 0, $cepRemFormatado, 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Grava array com pedidos impressos
            $pedidosImpressos[] = $pedido;

            // Controle de etiquetas impressas
            $etiqImpressa++;
        }

        // Marca pedidos como impressos
        foreach ($pedidosImpressos as $numPed) {
            $dados = array(
                'fk_etiq_ender'   => 1,
            );

            Db::getInstance()->update('orders', $dados, 'id_order = '.(int)$numPed);
        }

        // Grava controle PDF
        $this->gravaControlePdf($nomePdf, $dataCriacaoPdf);

        // Fecha e gera PDF
        //$pdf->Output($nomePdf, 'I');
        $pdf->Output(_PS_MODULE_DIR_.FK_NOME_MODULO.'/pdf/'.$nomePdf, 'F');

    }

    public function geraEtiquetas_4($pedidos) {

        if (!$pedidos) {
            return false;
        }

        // Nome do PDF
        $nomePdf = 'etiquetaEnder_'.uniqid().'.pdf';

        // Data PDF
        $dataCriacaoPdf = date('Y-m-d H:i:s');

        if (!is_array($pedidos)) {
            $pedidos = array('0' => $pedidos);
        }

        // Instancia TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

        // Informacoes do documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Fokusfirst');
        $pdf->SetTitle('Etiquetas de Endereçamento');

        // Margens
        $marginLeft = 2;
        $marginTop = 2;
        $marginRight = 2;
        $pdf->SetMargins($marginLeft, $marginTop, $marginRight);

        // Remove header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Fator de redimensionamento de imagens
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $styleBarCodeDM = array(
            'border' => false,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        );

        $styleBarCode128 = array(
            'stretch' => false,
            'fitwidth' => true,
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false,
        );

        $styleRetangulo = array(
            'width' => 0.5,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => array(0, 0, 0)
        );

        $styleFlexa = array(
            'width' => 0.25,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => array(0, 0, 0)
        );

        $styleLinhaRemetente = array(
            'width' => 0.3,
            'cap' => 'butt',
            'join' => 'miter',
            'color' => array(225, 225, 225)
        );

        $pedidosImpressos = array();
        $etiqImpressa = 1;

        foreach ($pedidos as $pedido) {

            // Variaveis de posicionamento
            $posRetangulo_x = 6;
            $posRetangulo_y = 7;

            $posLogo_x = 78;
            $posLogo_y = 7;

            $posBarCodeDM_x = 73;
            $posBarCodeDM_y = 15;

            $cellMarginLeft = 3;

            $posBarCode128_x = 30;
            $posBarCode128_y = 70;

            $posLinhaRemetente_x = 3;
            $posLinhaRemetente_y = 100;

            switch ($etiqImpressa) {

                case 1:
                    // adiciona pagina
                    $pdf->AddPage();

                    break;

                case 2:
                    $posRetangulo_x += 106;
                    $posLogo_x += 106;
                    $posBarCodeDM_x += 106;
                    $cellMarginLeft += 106;
                    $posBarCode128_x += 106;
                    $posLinhaRemetente_x += 106;

                    break;

                case 3:
                    $posRetangulo_y += 138;
                    $posLogo_y += 138;
                    $posBarCodeDM_y += 138;
                    $posBarCode128_y += 132;
                    $posLinhaRemetente_y += 132;

                    break;

                case 4:
                    $posRetangulo_x += 106;
                    $posRetangulo_y += 138;
                    $posLogo_x += 106;
                    $posLogo_y += 138;
                    $posBarCodeDM_x += 106;
                    $posBarCodeDM_y += 138;
                    $cellMarginLeft += 106;
                    $posBarCode128_x += 106;
                    $posBarCode128_y += 132;
                    $posLinhaRemetente_x += 106;
                    $posLinhaRemetente_y += 132;

                    $etiqImpressa = 0;

                    break;
            }

            // Variaveis de posicionamento
            $posLinhaRemetente_x1 = $posLinhaRemetente_x + 100;
            $posLinhaRemetente_y1 = $posLinhaRemetente_y;

            // Recupera dados do pedido
            $order = new Order($pedido);

            // Recupera dados do cadastro e endereco do cliente
            $dadosCliente = $this->recuperaEndereco($order->id_address_delivery);

            // CEP do destinatario
            $cepDestNaoFormatado = trim(preg_replace("/[^0-9]/", "", $dadosCliente['postcode']));

            if (strlen($cepDestNaoFormatado) == 8) {
                $cepDestFormatado = substr($cepDestNaoFormatado, 0, 5).'-'.substr($cepDestNaoFormatado, 5, 3);
            }

            // CEP do Remetente
            $cepRemNaoFormatado = trim(preg_replace("/[^0-9]/", "", trim(Configuration::get('FKCORREIOSG2CP2_CEP'))));
            $cepRemFormatado = substr($cepRemNaoFormatado, 0, 5).'-'.substr($cepRemNaoFormatado, 5, 3);

            // Numero Destinatario
            $numEnd = false;
            $numeroDest = '';

            if (module::isInstalled('fkcustomers')) {
                if (isset($dadosCliente['numend'])) {
                    if (trim($dadosCliente['numend']) != '') {
                        if (!Configuration::get('FKCUSTOMERS_MODO') or Configuration::get('FKCUSTOMERS_MODO') == '1') {

                            $numEnd = true;
                            $numeroDest = trim($dadosCliente['numend']);
                        }
                    }
                }
            }

            // Numero Remetente
            $numeroRem = trim(Configuration::get('FKCORREIOSG2CP2_NUMERO'));

            // reset na margem da celula
            $pdf->setCellMargins(0, 0, 0, 0);

            // Retangulo
            $pdf->SetLineStyle($styleRetangulo);

            $largura = 38;
            $altura = 6;
            $pdf->RoundedRect($posRetangulo_x, $posRetangulo_y, $largura, $altura, 1, '1111', 'DF', '',array(255, 255, 255));

            // Flexa
            $pdf->SetLineStyle($styleFlexa);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->Arrow($posRetangulo_x + 5, $posRetangulo_y + 8, $posRetangulo_x + 5, $posRetangulo_y + 11, 2, 5, 35);

            // Texto Destinatario
            $pdf->SetFont('helvetica','B', 10);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->Text($posRetangulo_x + 5, $posRetangulo_y + 1, $this->convUpper('Destinatário'));

            // Logo
            if (file_exists(FK_URI_IMG.'logo_4.jpg')) {
                $larguraImg = 21.17;
                $alturaImg = 5.56;
                $pdf->Image(FK_URI_IMG.'logo_4.jpg', $posLogo_x, $posLogo_y, $larguraImg, $alturaImg, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }

            // Codigo de barras Data Matrix
            $pdf->write2DBarcode($cepDestNaoFormatado, 'DATAMATRIX', $posBarCodeDM_x, $posBarCodeDM_y, 16, 16, $styleBarCodeDM, 'N');
            $numTmp = '00000';

            if (is_numeric($numeroDest)) {
                $numTmp = substr($numTmp.$numeroDest, -5);
            }
            $pdf->write2DBarcode($numTmp, 'DATAMATRIX', $posBarCodeDM_x + 16, $posBarCodeDM_y, 16, 16, $styleBarCodeDM, 'N');

            $pdf->write2DBarcode($cepRemNaoFormatado, 'DATAMATRIX', $posBarCodeDM_x, $posBarCodeDM_y + 16, 16, 16, $styleBarCodeDM, 'N');
            $numTmp = '00000';

            if (is_numeric($numeroRem)) {
                $numTmp = substr($numTmp.$numeroRem, -5);
            }
            $pdf->write2DBarcode($numTmp, 'DATAMATRIX', $posBarCodeDM_x + 16, $posBarCodeDM_y + 16, 16, 16, $styleBarCodeDM, 'N');

            // Fonte
            $pdf->SetFont('helvetica', '', 10);

            // margem da celula
            $pdf->setCellMargins($cellMarginLeft, 0, 0, 0);

            // Altera coordenada
            $pdf->SetAbsY($posRetangulo_y + 13);

            // Largura e Altura da celula
            $larguraCelula = 50;

            // Nome
            $nome = trim($dadosCliente['firstname']).' '.trim($dadosCliente['lastname']);
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($nome), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Endereco
            if ($numEnd) {
                $endereco = trim($dadosCliente['address1']).' '.$numeroDest;
            }else {
                $endereco = trim($dadosCliente['address1']);
            }

            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($endereco), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Complemento
            if (isset($dadosCliente['compl'])) {

                $tmp = trim($dadosCliente['compl']);

                if (!empty($tmp)) {
                    $complemento = $tmp;
                    $pdf->MultiCell($larguraCelula, 0, $this->convUpper($complemento), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);
                }
            }

            // Bairro
            $tmp = trim($dadosCliente['address2']);

            if (!empty($tmp)) {
                $bairro = $tmp;
                $pdf->MultiCell($larguraCelula, 0, $this->convUpper($bairro), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);
            }

            // Cidade e Estado
            $cidade = trim($dadosCliente['city']);
            $estado = trim($dadosCliente['iso_code']);
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($cidade.' / '.$estado), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // CEP
            if (strlen($cepDestNaoFormatado) == 8) {

                $pdf->MultiCell($larguraCelula, 0, $cepDestFormatado, 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

                // reset na margem da celula
                $pdf->setCellMargins(0, 0, 0, 0);

                // Codigo de barras 128
                $pdf->write1DBarcode($cepDestNaoFormatado, 'C128C', $posBarCode128_x, $posBarCode128_y, '', 25, 0.5, $styleBarCode128, 'N');

                // CEP abaixo do codigo de barras
                $pdf->SetFont('helvetica', '', 8);

                $pdf->Text($posBarCode128_x + 16, $posBarCode128_y + 23, $cepDestFormatado);
            }

            // Linha remetente
            $pdf->Line($posLinhaRemetente_x, $posLinhaRemetente_y, $posLinhaRemetente_x1, $posLinhaRemetente_y1, $styleLinhaRemetente);

            $pdf->SetFont('helvetica', 'B', 7);

            $pdf->Text($posLinhaRemetente_x + 2, $posLinhaRemetente_y + 1, $this->convUpper('Remetente:'));

            $pdf->SetFont('helvetica', '', 7);

            // Pedido e referencia
            $pdf->Text($posLinhaRemetente_x + 60, $posLinhaRemetente_y + 1, $this->convUpper('Pedido: '.$order->id.' ('.$order->reference.')'));

            // reset na margem da celula
            $pdf->setCellMargins(0, 0, 0, 0);

            // Largura e Altura da celula
            $larguraCelula = 80;

            // Nome remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 2);
            $pdf->SetAbsY($posLinhaRemetente_y + 4);
            $nome = trim(Configuration::get('FKCORREIOSG2CP2_REMETENTE'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($nome), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Endereco remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 2);
            $endereco = trim(Configuration::get('FKCORREIOSG2CP2_ENDERECO')).' '.trim(Configuration::get('FKCORREIOSG2CP2_NUMERO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($endereco), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Bairro remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 2);
            $bairro = trim(Configuration::get('FKCORREIOSG2CP2_BAIRRO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($bairro), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Cidade e Estado do remetente
            $pdf->SetAbsX($posLinhaRemetente_x + 2);
            $cidade = trim(Configuration::get('FKCORREIOSG2CP2_CIDADE'));
            $estado = trim(Configuration::get('FKCORREIOSG2CP2_ESTADO'));
            $pdf->MultiCell($larguraCelula, 0, $this->convUpper($cidade.' / '.$estado), 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // CEP do destinatario
            $pdf->SetAbsX($posLinhaRemetente_x + 2);
            $pdf->MultiCell($larguraCelula, 0, $cepRemFormatado, 0, 'L', 0, 1, '', '', true, 0, false, false, 0);

            // Grava array com pedidos impressos
            $pedidosImpressos[] = $pedido;

            // Controle de etiquetas impressas
            $etiqImpressa++;
        }

        // Marca pedidos como impressos
        foreach ($pedidosImpressos as $numPed) {
            $dados = array(
                'fk_etiq_ender'   => 1,
            );

            Db::getInstance()->update('orders', $dados, 'id_order = '.(int)$numPed);
        }

        // Grava controle PDF
        $this->gravaControlePdf($nomePdf, $dataCriacaoPdf);

        // Fecha e gera PDF
        //$pdf->Output($nomePdf, 'I');
        $pdf->Output(_PS_MODULE_DIR_.FK_NOME_MODULO.'/pdf/'.$nomePdf, 'F');

    }

    private function recuperaEndereco($id_address) {

        $sql =  'SELECT '._DB_PREFIX_.'address.*,'
                         ._DB_PREFIX_.'country_lang.name AS pais,'
                         ._DB_PREFIX_.'state.name AS estado,'
                         ._DB_PREFIX_.'state.iso_code '.
                'FROM '._DB_PREFIX_.'address
                    INNER JOIN '._DB_PREFIX_.'customer
                        ON '._DB_PREFIX_.'address.id_customer = '._DB_PREFIX_.'customer.id_customer
                    INNER JOIN '._DB_PREFIX_.'country_lang
                        ON '._DB_PREFIX_.'customer.id_lang = '._DB_PREFIX_.'country_lang.id_lang AND '._DB_PREFIX_.'address.id_country = '._DB_PREFIX_.'country_lang.id_country
                    INNER JOIN '._DB_PREFIX_.'state
                        ON '._DB_PREFIX_.'address.id_state = '._DB_PREFIX_.'state.id_state '.
                'WHERE '._DB_PREFIX_.'address.id_address = '.(int)$id_address;

        $dados = Db::getInstance()->getRow($sql);

        return $dados;
    }

    private function gravaControlePdf($nomePdf, $dataCriacaoPdf) {

        $dados = array(
            'id_shop'       => $this->context->shop->id,
            'arquivo_pdf'   => $nomePdf,
            'data_criacao'  => $dataCriacaoPdf
        );

        Db::getInstance()->insert('fkcorreiosg2cp2_etiquetas_ender', $dados);
    }

    private function convUpper($texto) {
        return mb_strtoupper(strtoupper($texto), 'UTF-8');
    }

}