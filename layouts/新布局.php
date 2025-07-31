<div class="container py-5">
    <div class="row">
        <!-- 顶部卡片 -->
        <div class="col-12">
            <!-- 个人简介 -->
            <div class="glass-card mb-5 text-center">
                <img src="<?= $data['profile']['image'] ?: 'http://q2.qlogo.cn/headimg_dl?dst_uin=1728031575&spec=100' ?>" class="profile-img mb-4" alt="个人照片">
                <h1 class="mb-3"><?= htmlspecialchars($data['profile']['name']) ?></h1>
                <h4 class="text-muted mb-4"><?= htmlspecialchars($data['profile']['title']) ?></h4>
                <p class="lead"><?= htmlspecialchars($data['profile']['bio']) ?></p>
                <div class="mt-4">
                    <?php foreach ($data['profile']['social'] as $platform => $link): ?>
                        <?php if (!empty($link)): ?>
                            <a href="<?= $link ?>" class="social-icon" target="_blank">
                                <i class="fab fa-<?= $platform ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 网格区域 -->
    <div class="row">
        <!-- 技能卡片 -->
        <div class="col-md-6 mb-4">
            <div class="glass-card h-100 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-laptop-code me-2"></i>专业技能</h2>
                <?php foreach ($data['skills'] as $skill): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <h5><?= htmlspecialchars($skill['name']) ?></h5>
                            <span><?= $skill['level'] ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-progress" style="width: <?= $skill['level'] ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 证书卡片 -->
        <div class="col-md-6 mb-4">
            <div class="glass-card h-100 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-award me-2"></i>证书认证</h2>
                <div class="row">
                    <?php foreach ($data['certifications'] as $cert): ?>
                        <div class="col-6 mb-3">
                            <div class="cert-card p-2 text-center">
                                <i class="fas fa-certificate fa-2x mb-2"></i>
                                <h6 class="mb-1"><?= htmlspecialchars($cert['name']) ?></h6>
                                <p class="small text-muted mb-1"><?= htmlspecialchars($cert['issuer']) ?></p>
                                <p class="small mb-0"><i class="fas fa-calendar me-1"></i><?= date('Y/m', strtotime($cert['date'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 教育卡片 -->
        <div class="col-md-6 mb-4">
            <div class="glass-card h-100 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-graduation-cap me-2"></i>教育经历</h2>
                <div class="timeline">
                    <?php foreach ($data['education'] as $edu): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= $edu['period'] ?></div>
                            <div class="timeline-content">
                                <h5><?= $edu['degree'] ?></h5>
                                <h6><?= $edu['school'] ?></h6>
                                <p><?= $edu['description'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 工作卡片 -->
        <div class="col-md-6 mb-4">
            <div class="glass-card h-100 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-briefcase me-2"></i>工作经历</h2>
                <div class="timeline">
                    <?php foreach ($data['experience'] as $exp): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= $exp['period'] ?></div>
                            <div class="timeline-content">
                                <h5><?= $exp['position'] ?></h5>
                                <h6><?= $exp['company'] ?></h6>
                                <p><?= $exp['description'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

<!-- 项目卡片 -->
<div class="col-12 mb-4">
    <div class="glass-card scrollable-card">
        <h2 class="mb-4"><i class="fas fa-project-diagram me-2"></i>精选项目</h2>
        <div class="row">
            <?php foreach ($data['projects'] as $project): ?>
                <div class="col-md-3 mb-4">
                    <div class="project-card h-100">
                        <img src="<?= $project['image'] ?: 'https://via.placeholder.com/400x200' ?>" class="project-img w-100" alt="<?= htmlspecialchars($project['title']) ?>">
                        <div class="p-3">
                            <h5><?= htmlspecialchars($project['title']) ?></h5>
                            <p class="small"><?= htmlspecialchars($project['description']) ?></p>
                            <?php if (!empty($project['link'])): ?>
                                <a href="<?= $project['link'] ?>" class="btn btn-sm btn-primary mt-auto align-self-start">查看详情</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 博客文章卡片 -->
<div class="col-12 mb-4">
    <div class="glass-card scrollable-card">
        <h2 class="mb-4"><i class="fas fa-blog me-2"></i>最新文章</h2>
        <div class="row">
            <?php foreach (($data['blogPosts'] ?? []) as $post): ?>
                <div class="col-md-3 mb-4">
                    <div class="project-card h-100">
                        <div class="p-3">
                            <h5><?= htmlspecialchars($post['title']) ?></h5>
                            <p class="small"><?= htmlspecialchars($post['excerpt']) ?></p>
                            <p class="small text-muted"><?= date('Y-m-d', strtotime($post['date'])) ?></p>
                            <a href="<?= $post['link'] ?>" class="btn btn-sm btn-primary mt-auto align-self-start">阅读全文</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
        <!-- 访客统计卡片 -->
        <div class="col-12 mb-4">
            <div class="glass-card scrollable-card">
                <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>访问统计</h2>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="info-card stat-card text-center p-4">
                            <i class="fas fa-users fa-2x mb-3"></i>
                            <h4><?= $visitorStats->getTotalVisits() ?></h4>
                            <p class="mb-0">总访问量</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="info-card stat-card text-center p-4">
                            <i class="fas fa-calendar-day fa-2x mb-3"></i>
                            <h4><?= $visitorStats->getTodayVisits() ?></h4>
                            <p class="mb-0">今日访问</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="info-card stat-card text-center p-4">
                            <i class="fas fa-map-marker-alt fa-2x mb-3"></i>
                            <h4>
                                <?php 
                                $referrers = $visitorStats->getTopReferrers(1);
                                echo htmlspecialchars(key($referrers));
                                ?>
                            </h4>
                            <p class="mb-0">主要来源</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 底部区域 -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <!-- 联系表单 -->
            <div class="glass-card h-100">
                <h2 class="mb-4"><i class="fas fa-envelope me-2"></i>联系我</h2>
                
                <!-- 表单提交结果消息 -->
                <?php if (isset($_SESSION['form_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['form_message']['type'] ?> mb-4">
                        <?= $_SESSION['form_message']['text'] ?>
                    </div>
                    <?php unset($_SESSION['form_message']); ?>
                <?php endif; ?>
                
                <form action="api.php?action=contact" method="POST">
                    <div class="mb-3">
                        <label class="form-label">姓名</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">邮箱</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">留言内容</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">发送留言</button>
                </form>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <!-- 每日名言 -->
            <div class="glass-card h-100 text-center d-flex flex-column justify-content-center">
                <i class="fas fa-quote-left me-2"></i>
                <span class="my-3"><?= $dailyQuote ?></span>
                <i class="fas fa-quote-right ms-2"></i>
            </div>
        </div>
    </div>
</div>