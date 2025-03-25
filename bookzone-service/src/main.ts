import { NestFactory } from '@nestjs/core';
import { BookZoneModule } from './bookzone/book-zone.module';

async function bootstrap() {
  const app = await NestFactory.create(BookZoneModule);
  await app.listen(process.env.PORT ?? 3001);
}
bootstrap();
