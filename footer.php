
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>    
      // 简历下载功能
        document.getElementById('download-resume')?.addEventListener('click', function() {
            const btn = this;
            const status = document.getElementById('resume-download-status');
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>生成中...';
            btn.disabled = true;
            status.textContent = '正在生成简历截图，请稍候...';
            status.className = 'mt-2 small text-info';
            
            // 获取截图模式设置
            const screenshotMode = '<?= $data['settings']['screenshot_mode'] ?? 'full' ?>';
            const targetElement = screenshotMode === 'container' ? document.querySelector('.container') : document.body;
            
            // 使用html2canvas生成截图
            html2canvas(targetElement, {
                scale: 2, // 提高分辨率
                useCORS: true, // 允许跨域图片
                logging: false,
                backgroundColor: null
            }).then(canvas => {
                // 将canvas转换为图片
                const imgData = canvas.toDataURL('image/png');
                
                // 创建下载链接
                const link = document.createElement('a');
                link.download = '<?= $data['profile']['name'] ?>_简历.png';
                link.href = imgData;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                btn.innerHTML = '<i class="fas fa-download me-2"></i>下载简历';
                btn.disabled = false;
                status.textContent = '简历下载完成！';
                status.className = 'mt-2 small text-success';
            }).catch(error => {
                console.error('简历生成失败:', error);
                btn.innerHTML = '<i class="fas fa-download me-2"></i>下载简历';
                btn.disabled = false;
                status.textContent = '简历生成失败，请重试';
                status.className = 'mt-2 small text-danger';
            });
        });

        // 加载每日名言
        async function loadDailyQuote() {
            try {
                const response = await fetch('api.php?action=get-quote');
                const data = await response.json();
                document.getElementById('daily-quote').textContent = data.quote;
            } catch (error) {
                console.error('加载名言失败:', error);
                document.getElementById('daily-quote').textContent = '保持热情，持续学习！';
            }
        }

        // 加载博客文章
        async function loadBlogPosts() {
            try {
                const response = await fetch('api.php?action=get-blog-posts');
                const posts = await response.json();
                let html = '';
                
                posts.forEach(post => {
                    html += `
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${post.title}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">${post.date}</h6>
                                    <p class="card-text">${post.excerpt}</p>
                                    <a href="${post.link}" class="btn btn-sm btn-outline-primary">阅读全文</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                document.getElementById('blog-posts').innerHTML = html;
            } catch (error) {
                console.error('加载博客失败:', error);
                document.getElementById('blog-posts').innerHTML = '<p class="text-center">暂时无法加载技术文章</p>';
            }
        }

        // 联系表单提交
        document.getElementById('contact-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const responseDiv = document.getElementById('contact-response');
            
            try {
                const response = await fetch('api.php?action=contact', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    responseDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    form.reset();
                } else {
                    responseDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                }
            } catch (error) {
                responseDiv.innerHTML = `<div class="alert alert-danger">请求失败，请稍后再试</div>`;
            }
        });
        
        // 应用主题的函数
        function applyTheme(theme) {
            // 移除所有主题类（使用更健壮的方式）
            const classes = document.body.classList;
            for (let i = classes.length - 1; i >= 0; i--) {
                const className = classes[i];
                if (className.startsWith('theme-')) {
                    document.body.classList.remove(className);
                }
            }
            
            // 添加当前主题类
            document.body.classList.add(theme);
            
            // 更新活动按钮状态
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.remove('active');
                if(btn.getAttribute('data-theme') === theme) {
                    btn.classList.add('active');
                }
            });
        }
        
        // 绑定主题按钮事件
        function bindThemeButtons() {
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const theme = this.getAttribute('data-theme');
                    applyTheme(theme);
                    
                    // 保存主题选择到localStorage
                    localStorage.setItem('selectedTheme', theme);
                    
                    // 保存主题到服务器
                    saveSettingToServer('theme', theme);
                });
            });
        }
        
        // 保存设置到服务器
        function saveSettingToServer(settingName, settingValue) {
            fetch('api.php?action=update-setting', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ 
                    setting: settingName,
                    value: settingValue 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`${settingName} 更新成功`);
                } else {
                    console.error(`${settingName} 更新失败`);
                }
            });
        }
        
        // 布局切换功能 - 仅管理员可用
        document.getElementById('layout-select')?.addEventListener('change', function() {
            const layout = this.value;
            
            // 保存布局到服务器
            saveSettingToServer('layout', layout);
            
            // 应用新布局
            const params = new URLSearchParams(window.location.search);
            params.set('layout', layout);
            window.location.search = params.toString();
        });
        
        // 页面加载时初始化
        document.addEventListener('DOMContentLoaded', async () => {
            // 应用保存的主题（优先使用服务器设置）
            const phpTheme = '<?= $data['settings']['theme'] ?? 'theme-purple' ?>';
            const savedTheme = localStorage.getItem('selectedTheme') || phpTheme;
            applyTheme(savedTheme);
// 仅管理员绑定主题按钮事件
            <?php if ($isAdmin): ?>
                bindThemeButtons();
            <?php endif; ?>
            
            // 加载数据
            loadDailyQuote();
            loadBlogPosts();
            
        });
    </script>
</body>
</html>