import { Module } from '@nestjs/common';
import { WebBookZoneAdapter } from './Infrastructure/Web/WebBookZoneAdapter';

@Module({
  imports: [],
  providers: [],
  controllers: [WebBookZoneAdapter],
})
export class BookZoneModule {}
