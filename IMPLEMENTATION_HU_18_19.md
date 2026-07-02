# HU-18 y HU-19 - Documentación de Implementación

## Descripción General
Este documento describe la implementación completa de dos historias de usuario críticas:

- **HU-18**: Visualización del recolector en camino (para usuarios)
- **HU-19**: Visualización de ruta de recogida (para recolectores)

---

## 📋 Contenido Implementado

### Backend (collectorUD)

#### 1. Migraciones de Base de Datos

**Archivo**: `database/migrations/2024_12_19_create_collector_locations_table.php`
- Tabla para almacenar ubicaciones en tiempo real del recolector
- Campos: `id`, `collector_id`, `latitude`, `longitude`, `created_at`, `updated_at`
- Índices para optimizar búsquedas

**Archivo**: `database/migrations/2024_12_19_create_pickup_points_table.php`
- Tabla para almacenar puntos de recogida dentro de órdenes
- Campos: `id`, `order_id`, `latitude`, `longitude`, `address`, `notes`, `completed_at`, `created_at`, `updated_at`

#### 2. Modelos

**CollectorLocation.php**
```php
- Relación con Collector
- Casting automático de coordenadas
- Timestamps para auditoría
```

**PickupPoint.php**
```php
- Relación con Order
- Soporte para completar puntos
- Almacenamiento de metadatos (notas)
```

**Order.php** (Nuevo)
```php
- Modelo central para órdenes de recogida
- Relaciones: user, collector, pickupPoints, wasteItems, state
- Gestión de estados y fechas programadas
```

**OrderTypeWaste.php** (Nuevo)
```php
- Tabla intermedia entre órdenes y tipos de residuos
- Almacena: weight, points
```

**Collector.php** (Actualizado)
```php
- Nueva relación: currentLocation() - última ubicación
- Nueva relación: locations() - historial de ubicaciones
- Nueva relación: orders() - órdenes asignadas
```

#### 3. Controladores API

**CollectorLocationController.php**
- `POST /api/collector/location` - Actualizar ubicación actual
- `GET /api/collector/location/{collectorId}` - Obtener ubicación actual
- `GET /api/collector/location/history/{collectorId}` - Obtener historial

**UserOrderTrackingController.php** (HU-18)
- `GET /api/my-collector-location` - Obtener recolector asignado y su ubicación
- `GET /api/my-active-orders` - Obtener todas las órdenes activas con ubicación

**CollectorPickupPointsController.php** (HU-19)
- `GET /api/collector/pickup-points` - Obtener puntos asignados al recolector
- `PATCH /api/pickup-point/{id}/complete` - Marcar punto como completado
- `GET /api/collector/order/{id}/details` - Obtener detalles de una orden

#### 4. Rutas API

Todos los endpoints están protegidos con `middleware('auth.jwt')`

```php
// HU-18: Tracking del Recolector (Usuario)
GET    /api/my-collector-location
GET    /api/my-active-orders

// HU-19: Puntos de Recogida (Recolector)
POST   /api/collector/location
GET    /api/collector/location/history/{collectorId}
GET    /api/collector/pickup-points
PATCH  /api/pickup-point/{pickupPoint}/complete
GET    /api/collector/order/{order}/details
```

---

### Frontend - AppUser (HU-18)

#### 1. Componente: CollectorTracking

**Ubicación**: `src/screens/CollectorTracking/`

**Funcionalidades**:
- ✅ Visualización en mapa de 2 marcadores:
  - 🟢 Recolector (icono de camión)
  - 🔵 Usuario (icono de ubicación)
- ✅ Actualizaciones automáticas cada 10 segundos
- ✅ Panel de información con detalles del recolector
- ✅ Botón de actualización manual
- ✅ Manejo de errores y loading states
- ✅ Ajuste automático del zoom para ver ambas ubicaciones

**Interfaz**:
```typescript
interface TrackingData {
  order_id: number;
  collector: {
    id: number;
    name: string;
    location: {
      latitude: number;
      longitude: number;
      updated_at: string;
    };
  };
  pickup_location: {
    latitude: number;
    longitude: number;
  };
}
```

#### 2. Servicio API: collectorTrackingApiService

**Ubicación**: `src/services/collectorTrackingApiService.ts`

**Métodos**:
- `getMyCollectorLocation()` - Obtener recolector asignado
- `getActiveOrders()` - Obtener todas las órdenes activas

**Características**:
- Interceptor automático para token JWT
- Manejo de errores centralizado
- Tipos TypeScript completos

---

### Frontend - AppRecicler (HU-19)

#### 1. Componente: Map (Actualizado)

**Ubicación**: `src/screens/Map/Map.tsx`

**Funcionalidades**:
- ✅ Visualización de múltiples marcadores:
  - 🟢 Recolector actual (icono de usuario)
  - 🔴 Puntos pendientes (icono de bolsa)
  - ✅ Puntos completados (icono de check, verde)
- ✅ Línea de ruta conectando los puntos
- ✅ Panel lateral con lista de puntos
- ✅ Estadísticas en tiempo real (pendientes/completados)
- ✅ Modal de detalles para cada punto
- ✅ Botón para marcar puntos como completados
- ✅ Actualización de ubicación cada 30 segundos
- ✅ Muestra información del usuario (nombre, teléfono)

**Características**:
- Auto-actualización de ubicación del recolector
- Envío de ubicación al servidor cada 30 segundos
- Gestión completa de puntos de recogida
- Interfaz intuitiva para completar recogidas

#### 2. Servicio API: collectorPickupApiService

**Ubicación**: `src/services/collectorPickupApiService.ts`

**Métodos**:
- `getMyPickupPoints()` - Obtener puntos asignados
- `updateMyLocation(location)` - Actualizar ubicación
- `completePickupPoint(pointId)` - Marcar completado
- `getOrderDetails(orderId)` - Obtener detalles
- `getLocationHistory(collectorId)` - Historial de ubicaciones

---

## 🔌 Integración

### Paso 1: Ejecutar Migraciones

```bash
cd /Users/jhoncuervo/Documents/GitHub/collectorUD
php artisan migrate
```

### Paso 2: AppUser - Integrar CollectorTracking

En `src/navigations/Navigation.tsx` o donde definas tus rutas:

```typescript
import { CollectorTracking } from '../screens/CollectorTracking';

// Agregar a tu stack de navegación
<Stack.Screen name="CollectorTracking" component={CollectorTracking} />
```

**Desde WastePickup** (para ir al tracking después de crear una orden):
```typescript
import { useNavigation } from '@react-navigation/native';

const navigation = useNavigation();
// Después de crear la orden exitosamente:
navigation.navigate('CollectorTracking');
```

### Paso 3: AppRecicler - Map ya está actualizado

El componente Map ya tiene toda la funcionalidad de HU-19 integrada. Solo asegúrate de que esté en tu navegación:

```typescript
import { Map } from '../screens/Map';

<Stack.Screen name="Map" component={Map} />
```

### Paso 4: Configurar URLs de API

En ambas apps, actualiza la URL base de la API:

**AppUser** - `src/services/collectorTrackingApiService.ts`:
```typescript
const API_BASE_URL = 'https://tu-dominio.com/api';
```

**AppRecicler** - `src/services/collectorPickupApiService.ts`:
```typescript
const API_BASE_URL = 'https://tu-dominio.com/api';
```

### Paso 5: Configurar Geolocalización

Asegúrate de que `getCurrentLocation` esté correctamente implementada:

**AppUser**: `src/functions/Geolocation.ts`
```typescript
export const getCurrentLocation = async (): Promise<LocationCoords | null> => {
  // Implementación existente
}
```

**AppRecicler**: `src/functions/Geolocation.ts`
```typescript
// Misma función, debe estar disponible
```

---

## 📱 Flujo de Uso

### HU-18: Usuario viendo al recolector

1. Usuario crea una solicitud de recogida en "Solicitud de Recogida"
2. Se crea una orden y se asigna un recolector
3. Usuario navega a "Ver Recolector" o visualiza en mapa
4. CollectorTracking muestra:
   - Ubicación del recolector (actualiza cada 10 segundos)
   - Distancia aproximada al punto de recogida
   - Nombre del recolector asignado
   - Información de la orden

### HU-19: Recolector viendo puntos de recogida

1. Recolector abre la app
2. Va a la pantalla "Mapa"
3. Ve todos sus puntos de recogida asignados
4. Puntos se muestran en colores:
   - 🔴 Pendiente
   - ✅ Completado
5. Al llegar a cada punto, lo marca como "Completado"
6. Su ubicación se envía al servidor cada 30 segundos

---

## 🔐 Seguridad y Validación

### Validación en Backend

```php
// CollectorLocationController
- latitude: required|numeric|between:-90,90
- longitude: required|numeric|between:-180,180

// CollectorPickupPointsController
- Verificación que el punto pertenece a una orden del recolector
- Solo recolectores pueden completar sus propios puntos
- Auditoría de completados (timestamps)
```

### Protección de Rutas

- Todas las rutas usan `middleware('auth.jwt')`
- Los usuarios solo ven sus propios datos
- Los recolectores solo ven sus propios puntos

---

## 📊 Bases de Datos - Diagrama Relacional

```
User
├── Collector (1-to-1)
│   ├── CollectorLocation (1-to-many)
│   └── Order (1-to-many)
│
└── Order (1-to-many)
    ├── PickupPoint (1-to-many)
    ├── OrderTypeWaste (1-to-many)
    └── Collector (optional, many-to-1)
```

---

## 🚀 Próximas Mejoras Recomendadas

1. **WebSockets para tiempo real**: En lugar de polling cada 10/30 segundos
   - Más eficiente
   - Actualizaciones instantáneas
   - Menos carga en servidor

2. **Notificaciones Push**:
   - Notificar usuario cuando recolector está cerca
   - Notificar recolector cuando llega a punto

3. **Historial y Estadísticas**:
   - Mapa de rutas completadas
   - Tiempo promedio por punto
   - Puntuación de eficiencia

4. **Optimización de Ruta**:
   - Sugerir orden óptimo de recogida
   - Algoritmo TSP (Traveling Salesman)

5. **Fotogrametría**:
   - Foto antes/después de recogida
   - Verificación de peso

---

## 🐛 Troubleshooting

### "No hay recolector asignado"
- Verificar que la orden tenga collector_id asignado
- Revisar que el recolector tenga estado activo

### "Error de conexión"
- Verificar URL de API
- Revisar credenciales JWT
- Comprobar permisos CORS

### Ubicación no se actualiza
- Verificar que getCurrentLocation esté funcionando
- Revisar permisos de geolocalización en app
- Comprobar que se está llamando a updateCollectorLocation

### Puntos no aparecen en mapa
- Verificar que existan pickup_points en la BD
- Comprobar que la orden esté asignada al recolector
- Revisar coordenadas (deben estar en rango válido)

---

## 📝 Testing

### Endpoints del API

```bash
# HU-18: Ver recolector asignado
GET /api/my-collector-location
Authorization: Bearer {token}

# HU-19: Obtener puntos de recogida
GET /api/collector/pickup-points
Authorization: Bearer {token}

# HU-19: Actualizar ubicación
POST /api/collector/location
Authorization: Bearer {token}
Content-Type: application/json
{
  "latitude": 4.7110,
  "longitude": -74.0721
}

# HU-19: Marcar completado
PATCH /api/pickup-point/1/complete
Authorization: Bearer {token}
```

---

## 📦 Archivos Creados/Modificados

### Backend
```
✅ database/migrations/2024_12_19_create_collector_locations_table.php
✅ database/migrations/2024_12_19_create_pickup_points_table.php
✅ app/Models/CollectorLocation.php
✅ app/Models/PickupPoint.php
✅ app/Models/Order.php
✅ app/Models/OrderTypeWaste.php
✅ app/Models/Collector.php (actualizado)
✅ app/Http/Controllers/CollectorLocationController.php
✅ app/Http/Controllers/UserOrderTrackingController.php
✅ app/Http/Controllers/CollectorPickupPointsController.php
✅ routes/api.php (actualizado)
```

### AppUser
```
✅ src/screens/CollectorTracking/CollectorTracking.tsx
✅ src/screens/CollectorTracking/index.ts
✅ src/services/collectorTrackingApiService.ts
```

### AppRecicler
```
✅ src/screens/Map/Map.tsx (actualizado)
✅ src/services/collectorPickupApiService.ts
```

---

## ✅ Checklist de Validación

- [ ] Migraciones ejecutadas sin errores
- [ ] Modelos Eloquent funcionando correctamente
- [ ] Endpoints API retornan datos válidos
- [ ] AppUser muestra mapa con recolector
- [ ] AppRecicler muestra puntos de recogida
- [ ] Ubicación se actualiza automáticamente
- [ ] Puntos se marcan como completados
- [ ] Manejo de errores funcionando
- [ ] Tokens JWT se validan correctamente
- [ ] CORS configurado correctamente

---

## 📞 Soporte

Para preguntas o problemas:
1. Revisar logs del servidor (`storage/logs/laravel.log`)
2. Revisar console de React Native
3. Validar estructura de datos en BD
4. Verificar autenticación JWT

---

**Fecha de Implementación**: 2024-12-19
**Versión**: 1.0
