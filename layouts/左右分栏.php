<div class="container py-5">
    <div class="row">
        <!-- 左侧栏 -->
        <div class="col-md-4">
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

            <!-- 技能部分 -->
            <div class="glass-card mb-5 scrollable-card">
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

            <!-- 联系表单 -->
            <div class="glass-card mb-5">
                <h2 class="mb-4"><i class="fas fa-envelope me-2"></i>联系我</h2>
                <form id="contact-form" method="POST">
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
                    <div id="contact-response" class="mt-3"></div>
                </form>
            </div>
        </div>

        <!-- 右侧栏 -->
        <div class="col-md-8">
            <!-- 工作经历 -->
            <div class="glass-card mb-5 scrollable-card">
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

            <!-- 项目展示 -->
            <div class="glass-card mb-5 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-project-diagram me-2"></i>精选项目</h2>
                <div class="row">
                    <?php foreach ($data['projects'] as $project): ?>
                        <div class="col-md-6 mb-4">
                            <div class="project-card h-100">
                                <img src="<?= $project['image'] ?: 'https://via.placeholder.com/400x200' ?>" class="project-img w-100" alt="<?= htmlspecialchars($project['title']) ?>">
                                <div class="p-3">
                                    <h5><?= htmlspecialchars($project['title']) ?></h5>
                                    <p><?= htmlspecialchars($project['description']) ?></p>
                                    <?php if (!empty($project['link'])): ?>
                                        <a href="<?= $project['link'] ?>" class="btn btn-sm btn-primary">查看详情</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 全宽内容 -->
    <div class="row">
        <div class="col-12">
            <!-- 每日名言 -->
            <div class="glass-card mb-5 text-center">
                <i class="fas fa-quote-left me-2"></i>
                <span id="daily-quote">加载中...</span>
                <i class="fas fa-quote-right ms-2"></i>
            </div>

            <!-- 教育经历 -->
            <div class="glass-card mb-5 scrollable-card">
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

            <!-- 证书部分 -->
            <div class="glass-card mb-5 scrollable-card">
                <h2 class="mb-4"><i class="fas fa-award me-2"></i>证书与认证</h2>
                <div class="row">
                    <?php foreach ($data['certifications'] as $cert): ?>
                        <div class="col-md-3 mb-4">
                            <div class="cert-card h-100 p-3 text-center">
                                <i class="fas fa-certificate fa-3x mb-3"></i>
                                <h5><?= htmlspecialchars($cert['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($cert['issuer']) ?></p>
                                <p><i class="fas fa-calendar me-2"></i><?= date('Y年m月', strtotime($cert['date'])) ?></p>
                                <?php if (!empty($cert['url'])): ?>
                                    <a href="<?= $cert['url'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        查看证书
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>