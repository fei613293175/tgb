(function () {
    'use strict';
    var config = window.tbPayOrderConfig || {};
    var currentOrder = null;
    var currentQrKey = '';
    var modal = null;

    function el(id) { return document.getElementById(id); }
    function setMessage(message) { el('tbResubmitMessage').textContent = message || ''; }

    function ensureModal() {
        if (modal) return;
        modal = document.createElement('div');
        modal.className = 'tb-resubmit-modal';
        modal.id = 'tbResubmitModal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML = '<section class="tb-resubmit-panel" role="dialog" aria-modal="true" aria-labelledby="tbResubmitTitle">' +
            '<header class="tb-resubmit-head"><div><span id="tbResubmitChannel">扫码支付</span><strong id="tbResubmitTitle">重新提交支付凭证</strong></div><button type="button" class="tb-resubmit-close" aria-label="关闭">&times;</button></header>' +
            '<div class="tb-resubmit-body"><div id="tbResubmitForm">' +
            '<div class="tb-resubmit-summary"><span id="tbResubmitSubject"></span><strong id="tbResubmitPrice"></strong></div>' +
            '<div class="tb-resubmit-warning">仅有1次重新提交机会。本次必须使用原支付渠道，提交后无法再次修改，请确认信息和截图无误。</div>' +
            '<div class="tb-resubmit-channel" id="tbResubmitFixedChannel"></div><div class="tb-resubmit-track" id="tbResubmitTrack"></div><div class="tb-resubmit-count" id="tbResubmitCount"></div>' +
            '<label class="tb-resubmit-field"><span id="tbResubmitNicknameLabel">支付账号网名</span><input type="text" id="tbResubmitNickname" maxlength="30" autocomplete="name"></label>' +
            '<label class="tb-resubmit-field"><span>真实姓名最后一个字</span><input type="text" id="tbResubmitRealname" maxlength="1" placeholder="例如：张三填写“三”"></label>' +
            '<label class="tb-resubmit-field tb-resubmit-proof"><input type="file" id="tbResubmitProof" accept="image/jpeg,image/png,image/gif,image/webp"><span class="tb-resubmit-preview" id="tbResubmitPreview">+</span><span><b>重新上传支付凭证截图</b><small>必填，支持 JPG / PNG / GIF / WEBP，最大 8MB</small></span></label>' +
            '<div class="tb-resubmit-message" id="tbResubmitMessage" role="status"></div><button type="button" class="tb-resubmit-submit" id="tbResubmitSubmit">确认重新提交</button></div>' +
            '<div class="tb-resubmit-success" id="tbResubmitSuccess" hidden><div class="tb-resubmit-success-mark">&#10003;</div><h3>重新提交成功</h3><p>凭证已进入审核队列，本订单的重新提交机会已经使用。</p><button type="button" class="tb-resubmit-submit" id="tbResubmitRefresh">完成并刷新订单</button></div></div></section>';
        document.body.appendChild(modal);
        modal.querySelector('.tb-resubmit-close').addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) { if (event.target === modal) closeModal(); });
        el('tbResubmitTrack').addEventListener('scroll', function () {
            var slides = this.querySelectorAll('.tb-resubmit-slide');
            if (!slides.length) return;
            var index = Math.max(0, Math.min(slides.length - 1, Math.round(this.scrollLeft / Math.max(this.clientWidth, 1))));
            currentQrKey = slides[index].getAttribute('data-qr-key');
            el('tbResubmitCount').textContent = (index + 1) + ' / ' + slides.length + (slides.length > 1 ? ' · 左右滑动选择' : '');
        }, {passive: true});
        el('tbResubmitProof').addEventListener('change', previewProof);
        el('tbResubmitSubmit').addEventListener('click', submitReview);
        el('tbResubmitRefresh').addEventListener('click', function () { window.location.reload(); });
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('tb-resubmit-lock');
    }

    function renderQrcodes(qrcodes) {
        var track = el('tbResubmitTrack');
        track.innerHTML = '';
        qrcodes.forEach(function (qrcode) {
            var slide = document.createElement('article');
            slide.className = 'tb-resubmit-slide';
            slide.setAttribute('data-qr-key', qrcode.key);
            var label = document.createElement('span');
            label.textContent = qrcode.label;
            var image = document.createElement('img');
            image.src = qrcode.url;
            image.alt = qrcode.label;
            image.loading = 'lazy';
            var download = document.createElement('a');
            download.className = 'tb-resubmit-download';
            download.href = qrcode.download_url;
            download.setAttribute('download', '');
            download.textContent = '下载二维码';
            slide.appendChild(label);
            slide.appendChild(image);
            slide.appendChild(download);
            track.appendChild(slide);
        });
        track.scrollLeft = 0;
        currentQrKey = qrcodes.length ? qrcodes[0].key : '';
        el('tbResubmitCount').textContent = qrcodes.length ? '1 / ' + qrcodes.length + (qrcodes.length > 1 ? ' · 左右滑动选择' : '') : '暂无可用收款码';
    }

    function previewProof() {
        var file = this.files && this.files[0];
        var preview = el('tbResubmitPreview');
        if (!file) return;
        if (!/^image\//.test(file.type) || file.size > 8 * 1024 * 1024) {
            this.value = '';
            preview.textContent = '+';
            preview.style.backgroundImage = '';
            setMessage('请选择不超过8MB的图片凭证');
            return;
        }
        preview.textContent = '';
        preview.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
        preview.style.backgroundSize = 'cover';
        preview.style.backgroundPosition = 'center';
        setMessage('');
    }

    function submitReview() {
        var data = currentOrder && currentOrder.resubmit_data;
        var nickname = el('tbResubmitNickname').value.trim();
        var realname = el('tbResubmitRealname').value.trim();
        var proof = el('tbResubmitProof').files[0];
        if (!data || !currentQrKey) return setMessage('原支付渠道暂时没有可用收款码');
        if (!nickname) return setMessage('请填写支付账号网名');
        if (Array.from(realname).length !== 1) return setMessage('请填写真实姓名最后一个字');
        if (!proof) return setMessage('请重新上传支付凭证截图');
        var form = new FormData();
        form.append('zftype', data.paytype);
        form.append('modac', 'pay');
        form.append('formhash', config.formhash || '');
        form.append('pluginid', data.pluginid);
        form.append('orderid', data.orderid);
        form.append('zd', data.zd || '');
        form.append('qr_key', currentQrKey);
        form.append('payer_nickname', nickname);
        form.append('realname_last', realname);
        form.append('payment_proof', proof);
        var button = el('tbResubmitSubmit');
        button.disabled = true;
        button.textContent = '正在提交...';
        setMessage('');
        fetch('plugin.php?id=tb_pay', {method: 'POST', body: form, credentials: 'same-origin'})
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (Number(result.code) !== 200) throw new Error(result.msg || '重新提交失败');
                el('tbResubmitForm').hidden = true;
                el('tbResubmitSuccess').hidden = false;
            })
            .catch(function (error) { setMessage(error.message || '网络异常，请稍后重试'); })
            .then(function () { button.disabled = false; button.textContent = '确认重新提交'; });
    }

    window.tbPayOpenResubmit = function (order) {
        ensureModal();
        if (!order || Number(order.can_resubmit) !== 1 || !order.resubmit_data) return;
        currentOrder = order;
        var data = order.resubmit_data;
        el('tbResubmitChannel').textContent = data.channel_name;
        el('tbResubmitFixedChannel').textContent = '原支付渠道：' + data.channel_name + '（不可更换）';
        el('tbResubmitNicknameLabel').textContent = Number(data.paytype) === 11 ? '支付宝网名' : '微信网名';
        el('tbResubmitSubject').textContent = order.subject;
        el('tbResubmitPrice').textContent = '￥' + order.price;
        el('tbResubmitNickname').value = '';
        el('tbResubmitRealname').value = '';
        el('tbResubmitProof').value = '';
        el('tbResubmitPreview').textContent = '+';
        el('tbResubmitPreview').style.backgroundImage = '';
        el('tbResubmitForm').hidden = false;
        el('tbResubmitSuccess').hidden = true;
        setMessage('');
        renderQrcodes(Array.isArray(data.qrcodes) ? data.qrcodes : []);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('tb-resubmit-lock');
    };
})();
