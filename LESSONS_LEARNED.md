# 踩坑与复用记录

## L-001 桌面 UA 不能代表真实 H5

- 现象：桌面访问根入口只显示二维码/引导。
- 原因：`xigua_hb` 按移动 UA 路由 touch 模板。
- 规则：页面体验和自动化必须使用真实移动 UA，并验证最终 URL/内容，而不是只缩小桌面窗口。

## L-002 移动 UA 可能在导航后丢失

- 现象：某些浏览器自动化导航后 UA 覆盖失效。
- 规则：每次关键导航前重新确认 UA；页面台账记录实际落地页面。

## L-003 GET 页面也可能写数据

- 现象：打开详情即提示红包进入钱包，并可能增加查看记录/计数。
- 规则：详情抓取视为有副作用；只用隔离账号/内容，前后对账，不做盲目爬虫。

## L-004 页面宽度异常不是单一模板问题

- 现象：提现约 405px、刷新套餐 406px、名片上传 486px，协议页窄列竖排。
- 规则：每页都跑 `scrollWidth <= clientWidth`；同时检查固定宽度、绝对定位、表格、图片、长词和旧样式。

## L-005 隐藏控件可能是业务依赖

- 现象：聊天/设置等页面 DOM 注入大量隐藏字段、弹层和提交控件。
- 规则：不能用“清理 DOM”方式删掉看似无用元素；先记录触发条件、字段和事件。

## L-006 CDN 脚本顺序会造成公开页故障

- 现象：注册/短信登录出现 `$ is not defined`，同时页面使用生产 Tailwind CDN。
- 规则：R02/R03 将依赖本地化并冻结加载顺序；修复脚本时不得改变表单协议。

## L-007 插件启用不等于页面可达

- 现象：启用插件、磁盘目录、运行时导航三者不一致。
- 规则：页面证据区分 `RUNTIME_REACHED`、`SOURCE_DISCOVERED`、`DORMANT_ENABLED` 和 `INTERACTIVE_ONLY`。

## L-008 老 Discuz 模板会穿透品牌

- 现象：联系资料页回退到旧 Discuz“提示信息”。
- 规则：建立通用旧模板适配层和页面台账，不只改 `xigua_hb` 主模板。

## L-009 品牌统一不能误改法律主体

- 现象：隐私/用户协议存在旧 `MKT` 文案。
- 规则：普通品牌位统一为“推广宝”；合同、支付、资质文字先获负责人/法务决定。

## L-010 浅色是硬要求，不是偏好

- 现象：第一版 Logo/主题概念含大面积深色。
- 纠正：正式改为“晴空电光 / Light Grid”，Logo v2 浅底。
- 规则：关版自动/人工检查大面积深色；被否决资产移出正式资源目录。

## L-011 App 安全区必须只有一个所有者

- 风险：原生和 H5 同时加状态栏高度会出现双空白；都不加会重叠。
- 决定：原生宿主拥有系统栏 insets，H5 App 环境不重复添加。

## L-012 相册权限应按系统版本最小化

- 风险：为了“上传图片”申请全盘存储，造成权限拒绝和审核风险。
- 规则：Photo Picker/SAF 优先；仅在回退路径按版本请求图片读取权限，不申请所有文件访问。

## L-013 第三方支付不能开放任意 scheme

- 风险：通用 `Intent.parseUri` 可被恶意页面滥用。
- 规则：scheme/host/package 三维白名单；订单成功只以服务端查询为准；SSL 错误不得绕过。

## L-014 APK 体积下限要在签名产物验证

- 风险：Debug、未签名或签名后追加文件的体积不能证明交付满足要求。
- 规则：最终签名 Release APK 检查 `>=10485760` 字节并记录哈希。

## L-015 Windows 向 Linux 传脚本必须规避 BOM/CRLF

- 现象：PowerShell 文本管道给远程 `sh` 时，首行出现 BOM，行尾 `\r` 破坏 `tail -n` 等参数。
- 规则：远程脚本先统一 LF，再以 UTF-8 Base64 传输并在服务器解码；执行前先跑 `bash -n`。

## L-016 宝塔数据库凭据不能从 SQLite 原始字段直接使用

- 现象：旧 `/www/server/panel/data/default.db` 已陈旧；当前 `/www/server/panel/data/db/default.db` 的敏感字段又经过面板层加密，直接读取原始 `mysql_root` 仍会认证失败。
- 规则：通过宝塔 `public.M('config')` 获取面板解密后的值，并在任何备份/建库动作前先执行无输出的 `SELECT 1`；认证失败必须在创建数据库前停止。失败留下的空目录改名隔离，不冒充快照。

## L-017 同机恢复部分库必须关闭 dump 的 GTID 注入

- 现象：MySQL 5.7 开启 GTID 时，默认部分库 `mysqldump` 写入 `GTID_PURGED`；导回同一实例会报 1840，因为 `GTID_EXECUTED` 非空。
- 规则：用于同实例预发布恢复的每个部分库快照固定添加 `--set-gtid-purged=OFF`，并必须以真实导入而非仅 `gzip -t` 证明可恢复。失败产生的预发布库、用户和目录只清理明确的固定名称，生产库不参与清理。

## L-018 宝塔 Python 3.7 不支持新版本 pathlib 参数

- 现象：面板 Python 3.7 的 `Path.write_text()` 不接受 `newline=`，配置改写在已恢复文件和数据库后停止。
- 规则：远程脚本只使用 Python 3.7 兼容 API；长流程必须保留可验证检查点，恢复成功后从检查点继续，避免因后处理兼容问题重复生成大快照。

## L-019 回环端口和 `.user.ini` 都必须在启用前验证

- 现象：计划端口 `127.0.0.1:18080` 已被 Docker 代理占用，请求命中无关 JSON 服务；复制的 `.user.ini` 又把 PHP `open_basedir` 固定在生产根目录。
- 规则：写 Nginx 配置前用 `ss` 验证端口空闲，启用后再验证监听进程是主机 Nginx；预发布必须重写 `.user.ini` 到 staging 根目录，禁止沿用生产 open_basedir。

## L-020 Web 根目录的父路径必须允许 Nginx 穿越

- 现象：站点文件已归属 `www`，但 staging 父目录为 `700 root`，Nginx `stat()` 返回 Permission denied 并伪装成 404。
- 规则：用 `namei -l` 检查从 `/` 到文件的每一级祖先；`/www/staging` 与项目父目录使用 `711`（仅穿越、不可列目录），站点文件按需归属 `www`，私密证据目录保持 `700 root`；HTTP 冒烟必须结合专属 error log，不能只看状态码猜原因。

## L-021 舞台健康检查必须跟随业务重定向

- 现象：H5 首跳正常返回 `302` 到登录页，若只接受首跳 `200` 会误报故障；舞台别名生成的 HTTPS 引用又可能丢失非标准端口。
- 规则：同时记录首跳、最终状态和重定向次数；回环舞台用明确端口完成最终 `200` 验证。进入交互阶段前另行冻结 HTTPS/Host 策略，不把只读验证路径直接当作支付回调地址。

## L-022 “目录存在”不等于 JDK 可用

- 现象：Android Studio `jbr` 目录存在，但缺少 `java.exe`/`keytool.exe`；旧临时 JDK 又因 DLL 不完整以 `0xc0000135` 退出。
- 规则：工具链门禁必须实际运行 `java`、`javac`、`keytool`、`adb`、`sdkmanager` 和 Gradle。系统安装被管理员确认阻断时，使用官方 ZIP 并校验 SHA-256，不依赖 PATH 猜测。

## L-023 公共模板覆盖会漏掉内联页面

- 现象：`xigua_member/profile.inc.php` 与 `view/module/site/sign.php` 直接输出大量 HTML/JS；静态协议和 Discuz 登录页也不经过 `xigua_hb/template/touch`。
- 规则：页面映射必须落到入口、控制器和最终模板/内联输出三层；公共主题通过不代表例外页通过，R03/R06/R08 必须分别验证。

## L-024 AGP 会主动阻断 Windows 中文路径

- 现象：工程位于中文用户名目录时，AGP 在配置阶段以“project path contains non-ASCII characters”停止，尚未进入源码编译。
- 规则：不为绕过而复制出第二套源码；在项目 `gradle.properties` 固定 `android.overridePathCheck=true`，随后以真实构建、单元测试、lint 和 APK 安装证明 UTF-8 路径可用。若后续工具仍不兼容，再使用文档化的 ASCII 构建镜像，而不是改交接包权威路径。

## L-025 compileSdk 与 targetSdk 必须分开决策

- 现象：`androidx.core 1.19.0` 的 AAR 元数据要求 compileSdk 37，初始 compileSdk 36 在依赖检查阶段停止。
- 规则：先按依赖/AGP 兼容矩阵提升 compileSdk，targetSdk 只在完成行为回归后提升；R01 固定 compileSdk 37、targetSdk 36，不能为“版本号看起来一致”同时改变运行时兼容行为。

## L-026 依赖“最新版”不能覆盖 minSdk 承诺

- 现象：`androidx.webkit 1.16.0` 将最低 API 提高到 24，Manifest 合并拒绝既定 minSdk 23。
- 规则：R01 锁定仍支持 API 23 的 `androidx.webkit 1.15.0`；不使用 `tools:overrideLibrary` 冒险绕过。依赖升级必须先确认 minSdk、WebView 行为和真机矩阵，lint 的版本提示属于有意约束而非待盲目消除。

## L-027 跨系统构建要覆盖平台专用依赖校验

- 现象：Windows 生成的 Gradle verification metadata 不含 Linux 专用 AAPT2；服务器容器在下载 `aapt2-...-linux.jar`/POM 时被严格校验正确拦截。
- 规则：不关闭依赖验证，也不直接信任失败缓存。分别计算服务器缓存与 Google Maven 官方下载的 SHA-256，二者一致后只补入对应 Linux 制品；Windows/Linux 构建均需再次通过严格校验。

## L-028 Windows 私有 properties 上传 Linux 时要清理 CR

- 现象：`keystore.properties` 的 CRLF 使服务器读取的 alias 尾部带 `\r`，安全断言提前停止且未开始构建。
- 规则：跨系统读取 properties 时只在内存中 `tr -d '\r'`，不输出值；alias、必填值和私有目录权限断言通过后才生成 root-only Docker env 文件。

## L-029 构建目录与二进制交付不能进入权威 manifest

- 现象：Gradle 会在 `android-app/build`、`android-app/.kotlin` 和模块 `build` 目录生成大量机器相关文件；服务器 APK 又不应提交 Git。
- 规则：权威 manifest 与 Git 同步排除可再生构建目录、CI 临时证据和 APK/AAB 二进制；源码、锁定文件、脚本、文档与脱敏文本证据仍必须完整登记。正式 APK 单独记录 SHA-256 和交付位置。

## L-030 模拟器通过“可用加速”检查仍可能无法开机

- 现象：本机 AEHD 报告 operational，但多个现有 AVD 和全新 Android 36 x86_64 AVD 在启动阶段退出；关闭硬件加速后设备长期停留 `offline`。
- 规则：`emulator -accel-check` 不能替代 `adb device`、`sys.boot_completed=1`、安装和启动证据。遇到主机运行时阻塞时保留日志，转由独立 GitHub Android 模拟器复验并等待实体机验收，不能把构建成功误报为启动成功。

## L-031 GitHub Actions 必须固定第三方 Action 提交

- 风险：只引用可移动 tag 会让同一仓库提交在未来执行未经审计的新 Action 代码。
- 规则：工作流中所有 Action 使用完整 commit SHA 并在注释中记录主版本；版本更新需重新核验来源、运行结果和依赖行为。

## L-032 构建进度反馈不应重复占用页面顶部

- 现象：实体机可正常访问，但 WebView 原生蓝色 3dp 进度条紧贴状态栏，形成多余的视觉噪声并暴露浏览器容器感。
- 规则：页面继续自动加载，原生容器不显示页面加载进度；错误只在确定主框架失败时显示浅色错误页。CI 增加源码门禁，禁止重新引入 `ProgressBar`/`onProgressChanged`。

## L-033 在线样式框架和图标 CDN 是首屏单点故障

- 现象：现有部分页面依赖在线 Tailwind/CDN，网络波动会造成样式或图标晚到、丢失并拖慢首屏。
- 规则：新 UI 不调用在线 Tailwind；通用样式编译/整理为本站版本化 CSS。图标采用本地 SVG 或精简 WOFF2 iconfont，设置内容哈希文件名和长期缓存，并对首屏请求、文件体积和 404 建门禁。

## L-034 平台专用 AAPT2 校验必须成对保留

- 现象：补入 Linux AAPT2 校验值时，若替换整个组件节点却遗漏已有 Windows jar，服务器构建通过但本机构建被严格校验拦截。
- 规则：同一 AAPT2 组件必须同时保留实际支持平台的 Linux/Windows 制品与 POM；每个新增值都用本机/服务器缓存和 Google Maven 官方下载双哈希确认。补一个平台时只能追加 artifact，不能覆盖另一个平台。

## L-035 服务器工具链镜像的 entrypoint 和签名挂载是接口

- 现象：`hhy-android-toolchain:r08-api36-cache` 自带 `/bin/bash` entrypoint；重复传 `bash -lc` 会把 Bash 二进制当脚本。既有签名 env 又把 keystore 固定为 `/run/tgb-signing/...`，换挂载点会在 `validateSigningRelease` 停止。
- 规则：先用 `docker image inspect` 记录 entrypoint/Cmd，只向现有 Bash 传 `-lc`；签名私有目录始终只读挂载到 env 声明的固定容器路径。不得为了通用脚本修改或输出签名秘密。

## L-036 GitHub Android runner 不保证 sdkmanager 裸命令在 PATH

- 现象：首个 Actions run 在 Android SDK 安装步骤以退出码 127 停止，runner 有 `$ANDROID_HOME`，但无法直接执行 `sdkmanager`。
- 初步处理：曾尝试从 `$ANDROID_HOME/cmdline-tools/**/bin/sdkmanager` 定位并断言可执行文件，但第二次 run 证明 runner 可能根本未预装 cmdline-tools；最终规则由 L-037 加固。前序失败没有产物时 Artifact 只警告，避免二次错误遮蔽根因。

## L-037 runner 没有 cmdline-tools 时不要继续猜路径

- 现象：在 `$ANDROID_HOME/cmdline-tools` 递归寻找 `sdkmanager` 的第二次 CI 尝试仍找不到可执行文件，以退出码 1 停止。
- 规则：runner 缺少命令行工具时使用固定完整 commit SHA 的 `android-actions/setup-android` v4 安装官方 Command-line Tools，并在 Action 输入中锁定平台/Build Tools；不要再假定预装目录结构。

## L-038 SDK 平台包名不能从 compileSdk 整数猜测

- 现象：固定版 `setup-android` 已正确安装并执行 `sdkmanager`；默认通道和 `--channel=3` 都返回 `Failed to find package 'platforms;android-37'`。服务器已通过镜像的实际条目为 `platforms;android-37.0`。
- 规则：先用 `sdkmanager --list` 或已通过环境确定精确包 ID，再安装并断言实际 `android.jar` 路径。Gradle 的 `compileSdk = 37` 不代表 SDK 包一定名为 `android-37`；不得通过静默降低 `compileSdk`、依赖或 target 行为绕过包名错误。

## L-039 依赖校验必须覆盖干净 Linux 首次解析的父 POM 与 module

- 现象：本机和服务器缓存构建已通过，但 GitHub 干净 Linux 环境首次解析 `guava-parent` POM 与 `junit-bom` module 时，严格校验正确停止。
- 规则：不能关闭 dependency verification。对每个新出现的 POM/module 从两个 Maven Central HTTPS 端点下载并确认字节 SHA-256 一致，只追加精确 artifact；随后用新的干净 CI run 证明元数据闭合。

## 可复用优点

- `xigua_hb` 已具备统一 touch 模板目录，可建立令牌层渐进迁移。
- 当前底部四导航的业务信息架构清晰，可保留顺序和功能，仅换视觉。
- WeUI 的部分交互行为可保留，外观通过受控覆盖统一。
- 页面族按公共、发现、内容、账户、资金、增长划分后，可共享组件并降低 164 模板重复劳动。
