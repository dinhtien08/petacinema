<div class="row">

                <!-- Tên phim -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên phim <span class="text-danger">*</span></label>

                    <input
                        type="text"
                        name="title"
                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['title'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['title'] ?? '' ?>
                    </div>
                </div>

                <!-- Thể loại -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Thể loại <span class="text-danger">*</span></label>

                    <input
                        type="text"
                        name="genres"
                        class="form-control <?= isset($errors['genres']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['genres'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['genres'] ?? '' ?>
                    </div>
                </div>

                <!-- Thời lượng -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Thời lượng (phút)</label>

                    <input
                        type="number"
                        name="duration"
                        class="form-control <?= isset($errors['duration']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['duration'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['duration'] ?? '' ?>
                    </div>
                </div>

                <!-- Ngày khởi chiếu -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày khởi chiếu</label>

                    <input
                        type="date"
                        name="release_date"
                        class="form-control <?= isset($errors['release_date']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['release_date'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['release_date'] ?? '' ?>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Trạng thái</label>

                    <select
                        name="status"
                        class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">

                        <option value="">-- Chọn trạng thái --</option>

                        <option value="coming_soon"
                            <?= ($data['status'] ?? '') == 'coming_soon' ? 'selected' : '' ?>>
                            Sắp chiếu
                        </option>

                        <option value="now_showing"
                            <?= ($data['status'] ?? '') == 'now_showing' ? 'selected' : '' ?>>
                            Đang chiếu
                        </option>

                        <option value="ended"
                            <?= ($data['status'] ?? '') == 'ended' ? 'selected' : '' ?>>
                            Ngừng chiếu
                        </option>

                    </select>

                    <div class="invalid-feedback">
                        <?= $errors['status'] ?? '' ?>
                    </div>
                </div>

                <!-- Ngôn ngữ -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngôn ngữ</label>

                    <input
                        type="text"
                        name="language"
                        class="form-control <?= isset($errors['language']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['language'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['language'] ?? '' ?>
                    </div>
                </div>

                <!-- Độ tuổi -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Giới hạn độ tuổi <span class="text-danger">*</span>
                    </label>

                    <select
                        name="age_rating"
                        class="form-select <?= isset($errors['age_rating']) ? 'is-invalid' : '' ?>">

                        <option value="">-- Chọn giới hạn độ tuổi --</option>

                        <option value="P" <?= ($data['age_rating'] ?? '') === 'P' ? 'selected' : '' ?>>
                            P - Phổ biến cho mọi lứa tuổi
                        </option>

                        <option value="K" <?= ($data['age_rating'] ?? '') === 'K' ? 'selected' : '' ?>>
                            K - Dưới 13 tuổi xem cùng người giám hộ
                        </option>

                        <option value="T13" <?= ($data['age_rating'] ?? '') === 'T13' ? 'selected' : '' ?>>
                            T13 - Khán giả từ đủ 13 tuổi
                        </option>

                        <option value="T16" <?= ($data['age_rating'] ?? '') === 'T16' ? 'selected' : '' ?>>
                            T16 - Khán giả từ đủ 16 tuổi
                        </option>

                        <option value="T18" <?= ($data['age_rating'] ?? '') === 'T18' ? 'selected' : '' ?>>
                            T18 - Khán giả từ đủ 18 tuổi
                        </option>

                        <option value="C" <?= ($data['age_rating'] ?? '') === 'C' ? 'selected' : '' ?>>
                            C - Cấm phổ biến
                        </option>

                    </select>

                    <div class="invalid-feedback">
                        <?= $errors['age_rating'] ?? '' ?>
                    </div>
                </div>

                <!-- Đạo diễn -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đạo diễn</label>

                    <input
                        type="text"
                        name="director"
                        class="form-control <?= isset($errors['director']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['director'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['director'] ?? '' ?>
                    </div>
                </div>

                <!-- Diễn viên -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Diễn viên</label>

                    <input
                        type="text"
                        name="actors"
                        class="form-control <?= isset($errors['actors']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['actors'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['actors'] ?? '' ?>
                    </div>
                </div>

                <!-- Trailer -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Trailer (URL)</label>

                    <input
                        type="url"
                        name="trailer"
                        class="form-control <?= isset($errors['trailer']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($data['trailer'] ?? '') ?>">

                    <div class="invalid-feedback">
                        <?= $errors['trailer'] ?? '' ?>
                    </div>
                </div>

                <!-- Poster -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Poster</label>

                    <input
                        type="file"
                        name="poster"
                        class="form-control <?= isset($errors['poster']) ? 'is-invalid' : '' ?>">

                    <div class="invalid-feedback">
                        <?= $errors['poster'] ?? '' ?>
                    </div>

                    <img
                        id="posterPreview"
                        src="<?= !empty($data['poster'])
                            ? BASE_ASSETS_UPLOADS . $data['poster']
                            : 'https://placehold.co/200x300?text=Poster' ?>"
                        class="img-thumbnail mt-3"
                        style="max-width:200px">
                </div>

                <!-- Mô tả -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mô tả</label>

                    <textarea
                        name="description"
                        rows="5"
                        class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>

                    <div class="invalid-feedback">
                        <?= $errors['description'] ?? '' ?>
                    </div>
                </div>

            </div>