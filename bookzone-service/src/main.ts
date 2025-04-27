import { NestFactory } from '@nestjs/core';
import { BookZoneModule } from './bookzone/book-zone.module';

async function bootstrap() {
  const app = await NestFactory.create(BookZoneModule, {
    cors: {
      origin: ['http://localhost:3000'],
      methods: ['GET', 'POST', 'PUT', 'DELETE'],
      allowedHeaders: ['Content-Type', 'Authorization'],
      exposedHeaders: ['Authorization'],
      credentials: true,
      maxAge: 3600
    }
  });
  await app.listen(process.env.PORT ?? 3001);
}
bootstrap();
