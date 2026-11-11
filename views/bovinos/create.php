<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Registrar Alta Individual de Bovino (CU-02 / RF-02)</h5>
                <a href="index.php?action=bovinos" class="btn btn-sm btn-light">Volver al Listado</a>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?action=bovino_create">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">RP / Carimba (*) <small class="text-muted">(RN-01)</small></label>
                            <input type="text" name="rp" class="form-control form-control-lg" placeholder="Ej: BOV-0105" value="<?= htmlspecialchars($_POST['rp'] ?? '') ?>" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Nombre / Apodo (Opcional)</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" placeholder="Ej: Lucero" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Fecha de Nacimiento (*)</label>
                            <input type="date" name="fecha_nacimiento" class="form-control form-control-lg" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Sexo (*)</label>
                            <select name="sexo" class="form-select form-select-lg" required>
                                <option value="H" <?= ($_POST['sexo'] ?? '') === 'H' ? 'selected' : '' ?>>Hembra (H)</option>
                                <option value="M" <?= ($_POST['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Macho (M)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Categoría Zootécnica (*)</label>
                            <select name="categoria" class="form-select form-select-lg" required>
                                <option value="ternero">Ternero/a</option>
                                <option value="desmamante">Desmamante</option>
                                <option value="vaquilla">Vaquilla</option>
                                <option value="novillo">Novillo</option>
                                <option value="vaca">Vaca</option>
                                <option value="toro">Toro</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Raza (*)</label>
                            <input type="text" name="raza" class="form-control form-control-lg" placeholder="Brangus, Nelore, Holando..." value="<?= htmlspecialchars($_POST['raza'] ?? 'Brangus') ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Propósito Productivo (*)</label>
                            <select name="proposito" class="form-select" required>
                                <option value="carne">Producción de Carne</option>
                                <option value="leche">Producción de Leche</option>
                                <option value="doble_proposito">Doble Propósito</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Régimen de Alimentación (*)</label>
                            <select name="sistema_alimentacion" class="form-select" required>
                                <option value="pastura_libre">Pastura Libre (Potrero)</option>
                                <option value="semiconfinamiento">Semiconfinamiento (Pastura + Suplemento)</option>
                                <option value="confinamiento_encierro">Confinamiento / Encierro (Feedlot)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Peso Inicial en Báscula (kg)</label>
                            <input type="number" step="0.01" min="15" max="1500" name="peso_inicial" class="form-control" placeholder="Ej: 185.50">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones de Campo / Potrero</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Ubicación, origen, genealogía o señas particulares..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="index.php?action=bovinos" class="btn btn-outline-secondary btn-touch px-4">Cancelar</a>
                        <button type="submit" class="btn btn-success btn-touch px-4 fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Guardar Bovino en el Hato
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>